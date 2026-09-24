<?php
namespace App\Http\Controllers;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Wallet;
use App\Models\VirtualCard;
use App\Services\AuditLogService;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdminCommerceController extends Controller {
 public function orders(){
  $orders=Order::with('user')->latest()->paginate(20);
  $couriers=DB::table('users')->join('roles','roles.name','=','users.role')->where('users.role','courier')->where('users.status','active')->select('users.id','users.name')->orderBy('users.name')->get();
  return view('admin.orders',compact('orders','couriers'));
 }
 public function updateOrder(Request $request,Order $order){
  $data=$request->validate(['status'=>'required|in:pending,processing,completed,cancelled,refunded','payment_status'=>'required|in:pending,unpaid,paid,failed,refunded','fulfillment_status'=>'required|in:unfulfilled,processing,shipped,delivered','courier_user_id'=>'nullable|integer|exists:users,id','tracking_number'=>'nullable|string|max:150']);
  if(!empty($data['courier_user_id'])) abort_unless(DB::table('users')->where('id',$data['courier_user_id'])->where('role','courier')->where('status','active')->exists(),422,'Selected courier is not active.');
  DB::transaction(function()use($request,$order,$data){
   $locked=Order::whereKey($order->id)->lockForUpdate()->first();$before=$locked->only(['status','payment_status','fulfillment_status']);$locked->update(collect($data)->except(['courier_user_id','tracking_number'])->all());
   $items=DB::table('order_items')->where('order_id',$locked->id)->get();
   foreach($items as $item){
    $method=DB::table('shipping_methods')->where('enabled',1)->orderByRaw("CASE WHEN code='manual' THEN 0 ELSE 1 END")->first();
    if(!$method) continue;
    $shipment=DB::table('shipments')->where('order_id',$locked->id)->where('vendor_id',$item->vendor_id)->lockForUpdate()->first();
    $status=$data['fulfillment_status']==='delivered'?'delivered':($data['fulfillment_status']==='shipped'?'shipped':'processing');
    $payload=['shipping_method_id'=>$method->id,'courier_user_id'=>$data['courier_user_id']??null,'tracking_number'=>$data['tracking_number']??($shipment->tracking_number??null),'status'=>$status,'updated_at'=>now()];
    if($status==='shipped' && !$shipment?->shipped_at)$payload['shipped_at']=now();
    if($status==='delivered'){$payload['shipped_at']=$shipment?->shipped_at ?: now();$payload['delivered_at']=now();}
    if($shipment) DB::table('shipments')->where('id',$shipment->id)->update($payload);
    else DB::table('shipments')->insert(array_merge(['order_id'=>$locked->id,'vendor_id'=>$item->vendor_id,'shipping_cost'=>0,'created_at'=>now()],$payload));
   }
   DB::table('notifications')->insert(['user_id'=>$locked->user_id,'type'=>'order.update','title'=>'Order update: '.$locked->order_number,'message'=>'Your order is now '.$locked->fulfillment_status.'.','data'=>json_encode(['order_id'=>$locked->id]),'created_at'=>now()]);
   if(!empty($data['courier_user_id'])) DB::table('notifications')->insert(['user_id'=>$data['courier_user_id'],'type'=>'shipment.assigned','title'=>'New shipment assigned','message'=>'Order '.$locked->order_number.' has been assigned to you.','data'=>json_encode(['order_id'=>$locked->id]),'created_at'=>now()]);
   app(AuditLogService::class)->log('order.updated','Order',$locked->id,['before'=>$before,'after'=>$data]);
  });
  return back()->with('success','Order, shipment and notifications updated.');
 }
 public function payments(){return view('admin.payments',['payments'=>Payment::with(['user','order'])->latest()->paginate(20)]);}
 public function updatePayment(Request $request,Payment $payment){$data=$request->validate(['status'=>'required|in:pending,paid,failed,refunded']);$before=$payment->only(['status','paid_at']);$payment->update($data);if($data['status']==='paid'){$payment->paid_at=now();$payment->save();$payment->order()->update(['payment_status'=>'paid']);}elseif($data['status']==='refunded'){$payment->order()->update(['payment_status'=>'refunded']);}app(AuditLogService::class)->log('payment.updated','Payment',$payment->id,['before'=>$before,'after'=>$data]);return back()->with('success','Payment updated.');}
 public function wallets(){return view('admin.wallets',['wallets'=>Wallet::with('user')->latest()->paginate(20)]);}
 public function adjustWallet(Request $request,Wallet $wallet){$data=$request->validate(['amount'=>'required|numeric|not_in:0','reason'=>'required|string|max:255']);$service=app(WalletService::class);if((float)$data['amount']>0)$service->credit($wallet,(float)$data['amount'],$data['reason'],'admin_adjustment',null);else $service->debit($wallet,abs((float)$data['amount']),$data['reason'],'admin_adjustment',null);app(AuditLogService::class)->log('wallet.adjusted','Wallet',$wallet->id,['amount'=>$data['amount'],'reason'=>$data['reason']]);return back()->with('success','Wallet balance adjusted.');}
 public function payouts(){
  $payouts=DB::table('vendor_payouts')->join('vendors','vendors.id','=','vendor_payouts.vendor_id')->join('users','users.id','=','vendors.user_id')->select('vendor_payouts.*','vendors.business_name','users.name as vendor_user_name')->latest('vendor_payouts.id')->paginate(25);
  return view('admin.payouts',compact('payouts'));
 }
 public function updatePayout(Request $request,int $id){
  $data=$request->validate(['status'=>'required|in:processing,paid,rejected,cancelled','notes'=>'nullable|string|max:1000']);
  DB::transaction(function()use($id,$data,$request){
   $payout=DB::table('vendor_payouts')->where('id',$id)->lockForUpdate()->first();
   abort_unless($payout,404);
   $current=$payout->status; $next=$data['status'];
   if($current===$next){
    DB::table('vendor_payouts')->where('id',$id)->update(['notes'=>$data['notes']??$payout->notes,'updated_at'=>now(),'processed_by'=>$request->user()->id,'processed_at'=>in_array($next,['paid','rejected','cancelled'],true)?now():$payout->processed_at]);
    return;
   }
   $terminal=['paid','rejected','cancelled'];
   abort_if(in_array($current,$terminal,true),422,'This payout is already finalized and cannot be changed.');
   if($next==='processing'){
    abort_unless(in_array($current,['requested','processing'],true),422,'Invalid payout status transition.');
   } elseif($next==='paid'){
    abort_unless(in_array($current,['requested','processing'],true),422,'Only requested or processing payouts can be marked paid.');
   } else {
    abort_unless(in_array($current,['requested','processing'],true),422,'Only requested or processing payouts can be cancelled or rejected.');
    $wallet=DB::table('wallets')->where('id',$payout->wallet_id)->lockForUpdate()->first();
    abort_unless($wallet,422,'Payout wallet no longer exists.');
    $alreadyRefunded=DB::table('wallet_transactions')->where('reference_type','vendor_payout_refund')->where('reference_id',$id)->exists();
    if(!$alreadyRefunded){
      $newBalance=(float)$wallet->balance+(float)$payout->amount;
      DB::table('wallets')->where('id',$wallet->id)->update(['balance'=>$newBalance,'updated_at'=>now()]);
      DB::table('wallet_transactions')->insert(['wallet_id'=>$wallet->id,'type'=>'credit','amount'=>$payout->amount,'balance_after'=>$newBalance,'reference_type'=>'vendor_payout_refund','reference_id'=>$id,'description'=>'Payout reservation refunded after '.$next,'created_at'=>now()]);
    }
   }
   DB::table('vendor_payouts')->where('id',$id)->update(['status'=>$next,'notes'=>$data['notes']??$payout->notes,'processed_by'=>$request->user()->id,'processed_at'=>in_array($next,['paid','rejected','cancelled'],true)?now():$payout->processed_at,'updated_at'=>now()]);
   app(AuditLogService::class)->log('vendor_payout.updated','VendorPayout',$id,['before'=>$current,'after'=>$next,'amount'=>$payout->amount,'vendor_id'=>$payout->vendor_id]);
  });
  return back()->with('success','Payout updated successfully.');
 }

 public function cards(){return view('admin.cards',['cards'=>VirtualCard::with('user')->latest()->paginate(20)]);}
 public function issueCard(Request $request){$data=$request->validate(['user_id'=>'required|integer|exists:users,id']);$existing=VirtualCard::where('user_id',$data['user_id'])->first();if($existing)return back()->with('error','This user already has a virtual marketplace card.');$number=null;do{$number='9';for($i=0;$i<15;$i++)$number.=random_int(0,9);}while(VirtualCard::where('display_number',$number)->exists());$card=VirtualCard::create(['user_id'=>$data['user_id'],'card_token'=>Str::random(64),'display_number'=>$number,'last4'=>substr($number,-4),'expiry_month'=>now()->addYears(3)->format('m'),'expiry_year'=>now()->addYears(3)->format('Y'),'status'=>'active','issued_at'=>now()]);app(AuditLogService::class)->log('virtual_card.issued','VirtualCard',$card->id,['user_id'=>$data['user_id'],'last4'=>$card->last4]);return back()->with('success','Virtual marketplace card issued.');}
}