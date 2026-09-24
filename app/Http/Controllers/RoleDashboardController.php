<?php
namespace AppHttpControllers;
use IlluminateHttpRequest;
use IlluminateSupportFacadesDB;

class RoleDashboardController extends Controller
{
 public function affiliate(Request $request){
  $user=$request->user();
  return view('role-dashboard',[
   'role'=>'Affiliate / Partner','eyebrow'=>'PARTNER CENTER',
   'intro'=>'Manage your referral activity, marketplace links and partner performance.',
   'stats'=>[['Referrals',DB::table('users')->where('id',$user->id)->count(), 'Account'],['Orders',DB::table('orders')->where('user_id',$user->id)->count(),'Referred customer orders'],['Status',ucfirst($user->status),'Account status']],
   'actions'=>[['Shop','Browse marketplace',route('shop')],['Live Shopping','Explore live commerce',route('live')],['Group Buying','Explore group offers',route('group-buying')],['My Account','Open customer account',route('customer.dashboard')]]
  ]);
 }
 public function courier(Request $request){
  $shipments=DB::table('shipments')->join('orders','orders.id','=','shipments.order_id')->join('vendors','vendors.id','=','shipments.vendor_id')->where('shipments.courier_user_id',$request->user()->id)->select('shipments.*','orders.order_number','orders.shipping_address','vendors.business_name')->latest('shipments.id')->paginate(20);
  return view('courier.dashboard',compact('shipments')); 
  /*
   'role'=>'Courier / Delivery Center','eyebrow'=>'DELIVERY OPERATIONS',
   'intro'=>'Delivery workspace for assigned marketplace fulfillment.',
   'stats'=>[['Assigned Shipments',DB::table('shipments')->where('courier_user_id',$request->user()->id)->count(),'My assigned shipments'],['Pending',DB::table('shipments')->where('courier_user_id',$request->user()->id)->whereIn('status',['pending','processing'])->count(),'My pending shipments'],['Status',ucfirst($request->user()->status),'Account status']],
   'actions'=>[['Marketplace Orders','View delivery order area',route('shop')],['Customer Account','Open account',route('customer.dashboard')],['Live Shopping','View marketplace live',route('live')]]
  ]);
  */
 }
 public function updateShipment(Request $request,int $id){
  $data=$request->validate(['status'=>'required|in:pending,picked_up,processing,shipped,delivered','tracking_number'=>'nullable|string|max:150']);
  DB::transaction(function()use($request,$id,$data){
   $s=DB::table('shipments')->where('id',$id)->where('courier_user_id',$request->user()->id)->lockForUpdate()->first();abort_unless($s,404);
   $payload=['status'=>$data['status'],'tracking_number'=>$data['tracking_number']??$s->tracking_number,'updated_at'=>now()];
   if($data['status']==='shipped'&&!$s->shipped_at)$payload['shipped_at']=now();
   if($data['status']==='delivered'){$payload['shipped_at']=$s->shipped_at?:now();$payload['delivered_at']=now();}
   DB::table('shipments')->where('id',$id)->update($payload);
   DB::table('orders')->where('id',$s->order_id)->update(['fulfillment_status'=>$data['status']==='delivered'?'delivered':($data['status']==='shipped'?'shipped':'processing'),'updated_at'=>now()]);
   $order=DB::table('orders')->where('id',$s->order_id)->first();
   DB::table('notifications')->insert(['user_id'=>$order->user_id,'type'=>'shipment.update','title'=>'Shipment update: '.$order->order_number,'message'=>'Your shipment is now '.$data['status'].'.','data'=>json_encode(['order_id'=>$order->id,'shipment_id'=>$id]),'created_at'=>now()]);
  });
  return back()->with('success','Shipment status updated.');
 }
 public function streamer(Request $request){
  return view('role-dashboard',[
   'role'=>'Live Streamer Center','eyebrow'=>'LIVE COMMERCE',
   'intro'=>'Live commerce workspace for approved streamers and marketplace content.',
   'stats'=>[['Live Streams',DB::table('live_streams')->count(),'Marketplace streams'],['Products',DB::table('products')->where('status','published')->count(),'Published products'],['Status',ucfirst($request->user()->status),'Account status']],
   'actions'=>[['Live Shopping','Open live marketplace',route('live')],['Group Buying','Explore offers',route('group-buying')],['Shop','Browse products',route('shop')],['Customer Account','Open account',route('customer.dashboard')]]
  ]);
 }
}