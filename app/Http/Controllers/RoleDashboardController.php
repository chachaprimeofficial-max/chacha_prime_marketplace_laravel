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
  return view('role-dashboard',[
   'role'=>'Courier / Delivery Center','eyebrow'=>'DELIVERY OPERATIONS',
   'intro'=>'Delivery workspace for assigned marketplace fulfillment.',
   'stats'=>[['Assigned Shipments',DB::table('shipments')->count(),'Marketplace shipments'],['Pending',DB::table('shipments')->whereIn('status',['pending','processing'])->count(),'Needs fulfillment'],['Status',ucfirst($request->user()->status),'Account status']],
   'actions'=>[['Marketplace Orders','View delivery order area',route('shop')],['Customer Account','Open account',route('customer.dashboard')],['Live Shopping','View marketplace live',route('live')]]
  ]);
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