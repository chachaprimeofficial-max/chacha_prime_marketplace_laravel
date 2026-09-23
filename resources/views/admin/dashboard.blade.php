@extends('admin.layout')
@section('title','Admin Control Center — Chacha Prime')
@section('page_heading','Marketplace Overview')
@section('content')
<style>.cp-chart{display:grid;gap:15px}.cp-chart-row{display:grid;grid-template-columns:95px 1fr 45px;gap:10px;align-items:center;font-size:12px;font-weight:800}.cp-chart-row i{height:10px;border-radius:99px;background:linear-gradient(90deg,#f59e0b,#fbbf24);display:block}.cp-chart-row b{text-align:right;font-size:12px}.cp-queue{display:grid;gap:10px}.cp-queue div{display:flex;justify-content:space-between;padding:12px;border:1px solid #e5e7eb;border-radius:10px;font-size:12px}.cp-queue b{font-size:15px}@media(max-width:600px){.cp-chart-row{grid-template-columns:75px 1fr 38px}}</style>
@if(session('success'))<div class="notice">{{session('success')}}</div>@endif
<div class="admin-grid">
@foreach([['Users',$stats['users'],'All registered accounts',''],['Customers',$stats['customers'],'B2C + B2B customers',''],['Vendors',$stats['vendors'],'Marketplace sellers',''],['Pending Vendors',$stats['pendingVendors'],'Awaiting review','warn'],['Products',$stats['products'],'Total catalog',''],['Pending Products',$stats['pendingProducts'],'Awaiting approval','warn'],['Orders',$stats['orders'],'Marketplace orders',''],['Paid Sales',number_format($stats['sales'],2),'Paid order value','money'],['Payments',$stats['payments'],'Payment records',''],['Pending Payments',$stats['pendingPayments'],'Needs attention','warn'],['Wallets',$stats['wallets'],'Active wallet records',''],['Virtual Cards',$stats['cards'],'Issued internal cards',''],['Reviews',$stats['reviews'],'Customer reviews',''],['Pending Reviews',$stats['pendingReviews'],'Moderation queue','warn'],['Group Campaigns',$stats['groupCampaigns'],'Buying campaigns',''],['Live Streams',$stats['liveStreams'],'Live commerce streams','']] as $s)
<div class="admin-stat {{$s[3]}}"><span class="label">{{$s[0]}}</span><strong>{{$s[1]}}</strong><small>{{$s[2]}}</small></div>
@endforeach
</div>

<section class="dashboard-two" style="margin-top:16px"><div class="panel"><div class="section-title" style="margin:0 0 14px"><div><h2>Marketplace performance</h2><p>Current operational volume at a glance.</p></div></div><div class="cp-chart"><div class="cp-chart-row"><span>Users</span><i style="width:{{min(100,max(5,(int)$stats['users']))}}%"></i><b>{{$stats['users']}}</b></div><div class="cp-chart-row"><span>Products</span><i style="width:{{min(100,max(5,(int)$stats['products']))}}%"></i><b>{{$stats['products']}}</b></div><div class="cp-chart-row"><span>Orders</span><i style="width:{{min(100,max(5,(int)$stats['orders']))}}%"></i><b>{{$stats['orders']}}</b></div><div class="cp-chart-row"><span>Vendors</span><i style="width:{{min(100,max(5,(int)$stats['vendors']))}}%"></i><b>{{$stats['vendors']}}</b></div></div></div><div class="panel"><div class="section-title" style="margin:0 0 14px"><div><h2>Attention queue</h2><p>Items currently needing review.</p></div></div><div class="cp-queue"><div><span>Pending vendors</span><b>{{$stats['pendingVendors']}}</b></div><div><span>Pending products</span><b>{{$stats['pendingProducts']}}</b></div><div><span>Pending payments</span><b>{{$stats['pendingPayments']}}</b></div><div><span>Pending reviews</span><b>{{$stats['pendingReviews']}}</b></div></div></div></section><div class="section-title"><div><h2>Quick operations</h2><p>Jump directly into the areas you manage most.</p></div></div>
<div class="quick-grid">
@foreach([
['Users & Customers','Manage accounts, roles and status','admin.users'],
['Vendors','Approve and manage sellers','admin.vendors'],
['Products','Review and manage catalog','admin.products'],
['Categories','Organize marketplace catalog','admin.categories'],
['Orders','Monitor and update orders','admin.orders'],
['Payments','Review payment activity','admin.payments'],
['Wallets','Manage marketplace wallets','admin.wallets'],
['Virtual Cards','Issue internal marketplace cards','admin.cards'],
['Group Buying','Campaign management','admin.module','group-buying'],
['Live Commerce','Streams and live selling','admin.module','live-commerce'],
['Shipping','Methods and fulfillment setup','admin.module','shipping'],
['Brands','Manage product brands','admin.module','brands'],
['Coupons','Discount and coupon controls','admin.module','coupons'],
['Currencies','Multi-currency settings','admin.module','currencies'],
['Payment Methods','Gateway configuration','admin.module','payment-methods'],
['Staff & Permissions','Team access control','admin.staff'],
['AI Activity','Gemini activity logs','admin.module','ai-logs'],
['Audit Logs','Administrative activity','admin.audit-logs'],
['Settings','Marketplace configuration','admin.module','settings'],
['CMS Pages','Store content management','admin.module','pages']
] as $q)
<a class="quick" href="{{isset($q[3])?route($q[2],$q[3]):route($q[2])}}"><em>→</em><b>{{$q[0]}}</b><span>{{$q[1]}}</span></a>
@endforeach
</div>

<div class="dashboard-two">
<section class="panel"><div class="section-title" style="margin:0 0 8px"><div><h2>Recent orders</h2><p>Latest marketplace activity</p></div><a href="{{route('admin.orders')}}">View all →</a></div><div class="table-wrap"><table><thead><tr><th>Order</th><th>Total</th><th>Payment</th><th>Status</th></tr></thead><tbody>
@forelse($recentOrders as $o)<tr><td><strong>{{$o->order_number}}</strong></td><td>{{$o->grand_total}} {{$o->currency}}</td><td><span class="status">{{$o->payment_status}}</span></td><td><span class="status">{{$o->status}}</span></td></tr>@empty<tr><td colspan="4">No orders yet.</td></tr>@endforelse
</tbody></table></div></section>
<section class="panel"><div class="section-title" style="margin:0 0 8px"><div><h2>Recent users</h2><p>Newest marketplace accounts</p></div><a href="{{route('admin.users')}}">View all →</a></div><div class="table-wrap"><table><thead><tr><th>Name</th><th>Role</th><th>Status</th></tr></thead><tbody>
@forelse($recentUsers as $u)<tr><td><strong>{{IlluminateSupportStr::limit($u->name,24)}}</strong></td><td><span class="status">{{$u->role}}</span></td><td><span class="status">{{$u->status}}</span></td></tr>@empty<tr><td colspan="3">No users yet.</td></tr>@endforelse
</tbody></table></div></section>
</div>
@endsection