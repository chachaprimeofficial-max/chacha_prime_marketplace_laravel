<?php
namespace App\Http\Controllers;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Wallet;
use App\Models\VirtualCard;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Services\AuditLogService;

class AdminCommerceController extends Controller
{
 public function orders(){return view('admin.orders',['orders'=>Order::with('user')->latest()->paginate(20)]);}
 public function updateOrder(Request $request,Order $order){$data=$request->validate(['status'=>'required|string|max:30','payment_status'=>'required|string|max:30','fulfillment_status'=>'required|string|max:30']);$before=$order->only(['status','payment_status','fulfillment_status']);$order->update($data);app(AuditLogService::class)->log('order.updated','Order',$order->id,['before'=>$before,'after'=>$data]);return back()->with('success','Order updated.');}
 public function payments(){return view('admin.payments',['payments'=>Payment::with(['user','order'])->latest()->paginate(20)]);}
 public function updatePayment(Request $request,Payment $payment){$data=$request->validate(['status'=>'required|string|max:30']);$before=$payment->only(['status','paid_at']);$payment->update($data);app(AuditLogService::class)->log('payment.updated','Payment',$payment->id,['before'=>$before,'after'=>$data]);if($data['status']==='paid'){$payment->paid_at=now();$payment->save();}return back()->with('success','Payment updated.');}
 public function wallets(){return view('admin.wallets',['wallets'=>Wallet::with('user')->latest()->paginate(20)]);}
 public function adjustWallet(Request $request,Wallet $wallet){$data=$request->validate(['amount'=>'required|numeric','reason'=>'required|string|max:255']);$wallet->balance=(float)$wallet->balance+(float)$data['amount'];$wallet->save();app(AuditLogService::class)->log('wallet.adjusted','Wallet',$wallet->id,['amount'=>$data['amount'],'reason'=>$data['reason'],'balance_after'=>$wallet->balance]);return back()->with('success','Wallet balance adjusted.');}
 public function cards(){return view('admin.cards',['cards'=>VirtualCard::with('user')->latest()->paginate(20)]);}
 public function issueCard(Request $request){$data=$request->validate(['user_id'=>'required|integer']);$number='9'.str_pad((string)$data['user_id'],15,'0',STR_PAD_LEFT);$card=VirtualCard::create(['user_id'=>$data['user_id'],'card_token'=>Str::random(64),'display_number'=>$number,'last4'=>substr($number,-4),'expiry_month'=>now()->addYears(3)->format('m'),'expiry_year'=>now()->addYears(3)->format('Y'),'status'=>'active','issued_at'=>now()]);app(AuditLogService::class)->log('virtual_card.issued','VirtualCard',$card->id,['user_id'=>$data['user_id'],'last4'=>$card->last4]);return back()->with('success','Virtual marketplace card issued.');}
}