@extends('admin.layout')
@section('title','Admin Control Center — Chacha Prime')
@section('page_heading','Marketplace Overview')
@section('content')
@if(session('success'))<div class="notice">{{session('success')}}</div>@endif
<div class="admin-grid">
@foreach([['Users',$stats['users'],'All registered accounts',''],['Customers',$stats['customers'],'B2C + B2B customers',''],['Vendors',$stats['vendors'],'Marketplace sellers',''],['Pending Vendors',$stats['pendingVendors'],'Awaiting review','warn'],['Products',$stats['products'],'Total catalog',''],['Pending Products',$stats['pendingProducts'],'Awaiting approval','warn'],['Orders',$stats['orders'],'Marketplace orders',''],['Paid Sales',number_format($stats['sales'],2),'Paid order value','money'],['Payments',$stats['payments'],'Payment records',''],['Pending Payments',$stats['pendingPayments'],'Needs attention','warn'],['Wallets',$stats['wallets'],'Active wallet records',''],['Virtual Cards',$stats['cards'],'Issued internal cards',''],['Reviews',$stats['reviews'],'Customer reviews',''],['Pending Reviews',$stats['pendingReviews'],'Moderation queue','warn'],['Group Campaigns',$stats['groupCampaigns'],'Buying campaigns',''],['Live Streams',$stats['liveStreams'],'Live commerce streams','']] as $s)
<div class="admin-stat {{$s[3]}}"><span class="label">{{$s[0]}}</span><strong>{{$s[1]}}</strong><small>{{$s[2]}}</small></div>
@endforeach
</div>

<div class="section-title"><div><h2>Quick operations</h2><p>Jump directly into the areas you manage most.</p></div></div>
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