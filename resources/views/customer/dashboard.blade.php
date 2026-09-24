@extends('layouts.customer')
@section('title','Customer Dashboard — Chacha Prime')
@push('styles')
<style>
.cp-cdash-hero{background:linear-gradient(135deg,#0b1627 0%,#14263f 58%,#233b59 100%);border-radius:18px;padding:28px 30px;color:#fff;display:flex;align-items:center;justify-content:space-between;gap:24px;overflow:hidden;position:relative}
.cp-cdash-hero:after{content:"";position:absolute;width:300px;height:300px;border-radius:50%;right:-110px;top:-150px;background:#ffffff0b}
.cp-cdash-eyebrow{font-size:10px;letter-spacing:2px;font-weight:900;color:#f7c873}.cp-cdash-hero h1{margin:7px 0 8px;font-size:30px;line-height:1.15;letter-spacing:-.7px}.cp-cdash-hero p{margin:0;max-width:650px;color:#cbd7e5;font-size:12px;line-height:1.65}.cp-cdash-actions{display:flex;gap:8px;flex-wrap:wrap;margin-top:17px}.cp-cdash-actions a{display:inline-flex;padding:10px 14px;border-radius:8px;text-decoration:none;font-size:11px;font-weight:900;background:#fff;color:#0f172a}.cp-cdash-actions a.alt{background:#ffffff12;color:#fff;border:1px solid #ffffff33}.cp-cdash-country{position:relative;z-index:2;min-width:180px;padding:16px;border:1px solid #ffffff22;border-radius:14px;background:#ffffff0a}.cp-cdash-country small,.cp-cdash-country span{display:block}.cp-cdash-country small{font-size:9px;color:#94a8bd;text-transform:uppercase;letter-spacing:.8px}.cp-cdash-country strong{display:block;font-size:18px;margin:5px 0}.cp-cdash-country span{font-size:10px;color:#cbd5e1}
.cp-cdash-kpis{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin:15px 0}.cp-cdash-kpi{background:#fff;border:1px solid #e2e8f0;border-radius:15px;padding:17px;text-decoration:none;color:#0f172a;box-shadow:0 7px 24px #0f172a08}.cp-cdash-kpi small{display:block;color:#64748b;font-size:9px;text-transform:uppercase;letter-spacing:.8px;font-weight:900}.cp-cdash-kpi strong{display:block;font-size:27px;margin-top:7px}.cp-cdash-kpi span{display:block;color:#94a3b8;font-size:10px;margin-top:4px}
.cp-cdash-grid{display:grid;grid-template-columns:minmax(0,1.7fr) minmax(280px,.8fr);gap:15px}.cp-cdash-card{background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:19px;box-shadow:0 7px 24px #0f172a08}.cp-cdash-card h2{font-size:16px;margin:0}.cp-cdash-head{display:flex;align-items:center;justify-content:space-between;gap:10px;margin-bottom:13px}.cp-cdash-head a{font-size:10px;color:#b45309;font-weight:900;text-decoration:none}.cp-cdash-order{display:grid;grid-template-columns:1.2fr .8fr .7fr auto;align-items:center;gap:10px;padding:13px 0;border-top:1px solid #edf2f7;font-size:11px}.cp-cdash-order strong{font-size:11px}.cp-status{display:inline-flex;width:max-content;padding:5px 8px;border-radius:999px;background:#f1f5f9;color:#475569;font-size:9px;font-weight:900;text-transform:uppercase}.cp-status.delivered,.cp-status.completed{background:#ecfdf5;color:#047857}.cp-status.pending,.cp-status.processing{background:#fffbeb;color:#a16207}.cp-cdash-order a{font-size:10px;color:#1769aa;font-weight:900;text-decoration:none}.cp-cdash-quick{display:grid;grid-template-columns:1fr 1fr;gap:9px}.cp-cdash-quick a{padding:14px;border:1px solid #e5eaf0;border-radius:11px;text-decoration:none;color:#0f172a}.cp-cdash-quick b{font-size:11px}.cp-cdash-quick span{display:block;font-size:9px;color:#64748b;line-height:1.45;margin-top:4px}.cp-cdash-service{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:13px;border:1px solid #e5eaf0;border-radius:11px;text-decoration:none;color:#0f172a;background:#fff}.cp-cdash-service+.cp-cdash-service{margin-top:8px}.cp-cdash-service b{font-size:11px}.cp-cdash-service span{display:block;color:#64748b;font-size:9px;margin-top:3px}.cp-cdash-service em{font-style:normal;font-size:9px;font-weight:900;color:#b45309}.cp-cdash-alert{padding:11px 0;border-top:1px solid #edf2f7;font-size:10px}.cp-cdash-alert strong{display:block;font-size:11px}.cp-cdash-alert span{display:block;color:#64748b;margin-top:3px}.cp-cdash-progress{display:grid;grid-template-columns:repeat(4,1fr);gap:6px;margin-top:12px}.cp-cdash-progress span{height:4px;background:#e2e8f0;border-radius:99px}.cp-cdash-progress span.active{background:#d28a16}.cp-cdash-address{padding:12px;border:1px solid #e5eaf0;border-radius:11px;background:#fbfcfd;font-size:10px;color:#475569;line-height:1.55}.cp-cdash-address+.cp-cdash-address{margin-top:8px}.cp-cdash-empty{padding:25px 10px;text-align:center;color:#94a3b8;font-size:11px}
@media(max-width:1050px){.cp-cdash-grid{grid-template-columns:1fr}.cp-cdash-country{min-width:160px}.cp-cdash-kpis{grid-template-columns:repeat(2,1fr)}}@media(max-width:650px){.cp-cdash-hero{padding:21px;display:block}.cp-cdash-hero h1{font-size:25px}.cp-cdash-country{margin-top:18px}.cp-cdash-kpis{grid-template-columns:1fr 1fr;gap:8px}.cp-cdash-kpi{padding:13px}.cp-cdash-kpi strong{font-size:23px}.cp-cdash-order{grid-template-columns:1fr auto}.cp-cdash-order .cp-order-meta{display:none}}@media(max-width:420px){.cp-cdash-kpis,.cp-cdash-quick{grid-template-columns:1fr}}
</style>
@endpush

<div class="cp-cdash-hero">
<div><div class="cp-cdash-eyebrow">MY CHACHA PRIME</div><h1>Welcome back, {{\Illuminate\Support\Str::limit($user->name,28)}}</h1><p>Your marketplace workspace for orders, saved products, account services and secure shopping. Everything important is available from one place.</p><div class="cp-cdash-actions"><a href="{{route('shop')}}">Continue shopping</a><a class="alt" href="{{route('customer.orders')}}">Track orders</a></div></div>
<div class="cp-cdash-country"><small>Marketplace</small><strong>{{session('marketplace_country_code','PK')}}</strong><span>Prices and availability follow your selected marketplace.</span></div>
</div>

<div class="cp-cdash-kpis">
<a class="cp-cdash-kpi" href="{{route('customer.orders')}}"><small>Total orders</small><strong>{{$stats['orders']}}</strong><span>All purchases</span></a>
<a class="cp-cdash-kpi" href="{{route('customer.orders')}}"><small>Needs attention</small><strong>{{$stats['pending']}}</strong><span>Pending or processing</span></a>
<a class="cp-cdash-kpi" href="{{route('customer.wishlist')}}"><small>Wishlist</small><strong>{{$stats['wishlist']}}</strong><span>Saved products</span></a>
<a class="cp-cdash-kpi" href="{{route('customer.notifications')}}"><small>Unread alerts</small><strong>{{$stats['notifications']}}</strong><span>Account updates</span></a>
</div>

<div class="cp-cdash-grid">
<section class="cp-cdash-card"><div class="cp-cdash-head"><h2>Recent orders</h2><a href="{{route('customer.orders')}}">View all orders</a></div>
@if($recentOrders->count())
@foreach($recentOrders as $order)
<div class="cp-cdash-order"><strong>Order #{{$order->id}}</strong><span class="cp-order-meta">{{number_format((float)$order->total,2)}} {{$order->currency ?? ''}}</span><span class="cp-status {{strtolower($order->status)}}">{{ucfirst($order->status)}}</span><a href="{{route('customer.order.detail',$order->id)}}">View order</a></div>
@endforeach
@else<div class="cp-cdash-empty">No orders yet. Start exploring the marketplace.</div>@endif
</section>

<aside class="cp-cdash-card"><div class="cp-cdash-head"><h2>Quick access</h2></div><div class="cp-cdash-quick">
<a href="{{route('customer.wishlist')}}"><b>Wishlist</b><span>Return to products you saved.</span></a>
<a href="{{route('customer.wallet')}}"><b>Wallet</b><span>Check balance and transactions.</span></a>
<a href="{{route('customer.card')}}"><b>Virtual Card</b><span>Open your marketplace card.</span></a>
<a href="{{route('customer.support')}}"><b>Support</b><span>Get help with orders and sellers.</span></a>
<a href="{{route('group-buying')}}"><b>Group Buying</b><span>Join active marketplace offers.</span></a>
<a href="{{route('live')}}"><b>Live Shopping</b><span>Discover products in real time.</span></a>
</div></aside>

<section class="cp-cdash-card"><div class="cp-cdash-head"><h2>Saved delivery addresses</h2><a href="{{route('customer.addresses')}}">Manage addresses</a></div>
@if($addresses->count()) @foreach($addresses as $a)<div class="cp-cdash-address"><strong>{{$a->recipient_name}}</strong>{{ $a->is_default ? ' • Default' : '' }}<br>{{$a->address_line1}}, {{$a->city}}@if($a->country), {{$a->country}}@endif<br>{{$a->phone}}</div>@endforeach @else<div class="cp-cdash-empty">No saved address. Add one before checkout.</div>@endif
</section>

<section class="cp-cdash-card">
<div class="cp-cdash-head"><h2>Account services</h2><span style="font-size:9px;color:#64748b">Manage your marketplace experience</span></div>
<div class="cp-cdash-service-list">
<a class="cp-cdash-service" href="{{route('customer.addresses')}}"><div><b>Address book</b><span>Manage delivery addresses and defaults.</span></div><em>Manage</em></a>
<a class="cp-cdash-service" href="{{route('customer.orders')}}"><div><b>Returns and refunds</b><span>Review return requests and order outcomes.</span></div><em>Orders</em></a>
<a class="cp-cdash-service" href="{{route('customer.notifications')}}"><div><b>Notifications</b><span>Order, delivery and marketplace updates.</span></div><em>Alerts</em></a>
<a class="cp-cdash-service" href="{{route('customer.support')}}"><div><b>Customer support</b><span>Get help with an order or marketplace issue.</span></div><em>Help</em></a>
</div>
</section>

<section class="cp-cdash-card">
<div class="cp-cdash-head"><h2>Order activity</h2><a href="{{route('customer.orders')}}">Open order history</a></div>
@if($recentOrders->count())
@foreach($recentOrders->take(3) as $order)
@php($state=strtolower((string)$order->status))
<div class="cp-cdash-alert"><strong>Order #{{$order->id}} — {{ucfirst($order->status)}}</strong><span>{{number_format((float)$order->total,2)}} {{$order->currency ?? ''}} · Updated {{IlluminateSupportCarbon::parse($order->updated_at)->diffForHumans()}}</span><div class="cp-cdash-progress"><span class="active"></span><span class="{{$state==='processing'||$state==='shipped'||$state==='delivered'?'active':''}}"></span><span class="{{$state==='shipped'||$state==='delivered'?'active':''}}"></span><span class="{{$state==='delivered'||$state==='completed'?'active':''}}"></span></div></div>
@endforeach
@else<div class="cp-cdash-empty">Order activity will appear here after your first purchase.</div>@endif
</section>

</div>
@endsection