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
 public function orders(){return view('admin.orders',['orders'=>Order::with('user')->latest()->paginate(20)]);}
 public function updateOrder(Request $request,Order $order){$data=$request->validate(['status'=>'required|in:pending,processing,completed,cancelled,refunded','payment_status'=>'required|in:pending,unpaid,paid,failed,refunded','fulfillment_status'=>'required|in:unfulfilled,processing,shipped,delivered']);$before=$order->only(['status','payment_status','fulfillment_status']);$order->update($data);app(AuditLogService::class)->log('order.updated','Order',$order->id,['before'=>$before,'after'=>$data]);return back()->with('success','Order updated.');}
 public function payments(){return view('admin.payments',['payments'=>Payment::with(['user','order'])->latest()->paginate(20)]);}
 public function updatePayment(Request $request,Payment $payment){$data=$request->validate(['status'=>'required|in:pending,paid,failed,refunded']);$before=$payment->only(['status','paid_at']);$payment->update($data);if($data['status']==='paid'){$payment->paid_at=now();$payment->save();$payment->order()->update(['payment_status'=>'paid']);}elseif($data['status']==='refunded'){$payment->order()->update(['payment_status'=>'refunded']);}app(AuditLogService::class)->log('payment.updated','Payment',$payment->id,['before'=>$before,'after'=>$data]);return back()->with('success','Payment updated.');}
 public function wallets(){return view('admin.wallets',['wallets'=>Wallet::with('user')->latest()->paginate(20)]);}
 public function adjustWallet(Request $request,Wallet $wallet){$data=$request->validate(['amount'=>'required|numeric|not_in:0','reason'=>'required|string|max:255']);$service=app(WalletService::class);if((float)$data['amount']>0)$service->credit($wallet,(float)$data['amount'],$data['reason'],'admin_adjustment',null);else $service->debit($wallet,abs((float)$data['amount']),$data['reason'],'admin_adjustment',null);app(AuditLogService::class)->log('wallet.adjusted','Wallet',$wallet->id,['amount'=>$data['amount'],'reason'=>$data['reason']]);return back()->with('success','Wallet balance adjusted.');}
 public function cards(){return view('admin.cards',['cards'=>VirtualCard::with('user')->latest()->paginate(20)]);}
 public function issueCard(Request $request){$data=$request->validate(['user_id'=>'required|integer|exists:users,id']);$existing=VirtualCard::where('user_id',$data['user_id'])->first();if($existing)return back()->with('error','This user already has a virtual marketplace card.');$number=null;do{$number='9';for($i=0;$i<15;$i++)$number.=random_int(0,9);}while(VirtualCard::where('display_number',$number)->exists());$card=VirtualCard::create(['user_id'=>$data['user_id'],'card_token'=>Str::random(64),'display_number'=>$number,'last4'=>substr($number,-4),'expiry_month'=>now()->addYears(3)->format('m'),'expiry_year'=>now()->addYears(3)->format('Y'),'status'=>'active','issued_at'=>now()]);app(AuditLogService::class)->log('virtual_card.issued','VirtualCard',$card->id,['user_id'=>$data['user_id'],'last4'=>$card->last4]);return back()->with('success','Virtual marketplace card issued.');}
}