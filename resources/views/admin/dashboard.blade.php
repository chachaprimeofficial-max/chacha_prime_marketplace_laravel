@extends('admin.layout')
@section('title','Admin Control Center — Chacha Prime')
@section('page_heading','Full Marketplace Control Center')
@section('content')
<div class="grid">
@foreach([['Users',$stats['users']],['Customers',$stats['customers']],['Vendors',$stats['vendors']],['Pending Vendors',$stats['pendingVendors']],['Products',$stats['products']],['Pending Products',$stats['pendingProducts']],['Orders',$stats['orders']],['Paid Sales',number_format($stats['sales'],2)],['Payments',$stats['payments']],['Pending Payments',$stats['pendingPayments']],['Wallets',$stats['wallets']],['Virtual Cards',$stats['cards']],['Reviews',$stats['reviews']],['Pending Reviews',$stats['pendingReviews']],['Group Campaigns',$stats['groupCampaigns']],['Live Streams',$stats['liveStreams']]] as $s)
<div class="stat"><small>{{$s[0]}}</small><strong>{{$s[1]}}</strong></div>
@endforeach
</div>
<div class="quick-grid">
@foreach([['Users','admin.users'],['Vendors','admin.vendors'],['Products','admin.products'],['Orders','admin.orders'],['Payments','admin.payments'],['Wallets','admin.wallets'],['Virtual Cards','admin.cards'],['Brands','admin.module','brands'],['Reviews','admin.module','reviews'],['Coupons','admin.module','coupons'],['Shipping','admin.module','shipping'],['Currencies','admin.module','currencies'],['Payment Methods','admin.module','payment-methods'],['Settings','admin.module','settings'],['CMS Pages','admin.module','pages'],['Group Buying','admin.module','group-buying'],['Live Commerce','admin.module','live-commerce'],['AI Logs','admin.module','ai-logs'],['Audit Logs','admin.module','audit-logs']] as $q)
<a class="quick" href="{{isset($q[2])?route($q[1],$q[2]):route($q[1])}}">{{$q[0]}} <span>→</span></a>
@endforeach
</div>
<div class="two">
<div class="panel"><h2>Recent Orders</h2><table><tr><th>Order</th><th>Total</th><th>Status</th></tr>@foreach($recentOrders as $o)<tr><td>{{$o->order_number}}</td><td>{{$o->grand_total}} {{$o->currency}}</td><td>{{$o->status}}</td></tr>@endforeach</table></div>
<div class="panel"><h2>Recent Users</h2><table><tr><th>Name</th><th>Role</th><th>Status</th></tr>@foreach($recentUsers as $u)<tr><td>{{$u->name}}</td><td>{{$u->role}}</td><td>{{$u->status}}</td></tr>@endforeach</table></div>
</div>
@endsection