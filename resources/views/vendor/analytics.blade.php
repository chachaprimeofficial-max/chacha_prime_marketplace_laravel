@extends('layouts.storefront')
@section('title','Analytics — Seller Center')
@push('styles')
<style>
.cp-an{background:#f5f7fa;min-height:calc(100vh - 104px);padding:22px}.cp-an-shell{max-width:1500px;margin:auto}.head,.card,.stat{background:#fff;border:1px solid #e5e7eb;border-radius:17px;box-shadow:0 7px 22px #11182706}.head{padding:21px 23px;display:flex;justify-content:space-between;align-items:center;gap:18px}.eyebrow{color:#d97706;font-size:10px;font-weight:900;letter-spacing:1.7px}.cp-an h1{margin:5px 0;font-size:29px;letter-spacing:-.7px}.head p{margin:0;color:#64748b;font-size:12px}.btn{display:inline-flex;padding:10px 14px;border-radius:10px;text-decoration:none;font-size:12px;font-weight:800;border:1px solid #dbe1e8;color:#1f2937;background:#fff}.btn.dark{background:#111827;color:#fff}.stats{display:grid;grid-template-columns:repeat(4,1fr);gap:13px;margin:15px 0}.stat{padding:17px}.stat span{display:block;color:#64748b;font-size:10px;font-weight:800}.stat strong{display:block;font-size:26px;margin-top:7px;letter-spacing:-.5px}.stat small{color:#94a3b8;font-size:10px}.stat.money strong{color:#047857}.card{padding:19px;margin-bottom:15px}.card h2{margin:0;font-size:17px}.sub{margin:4px 0 16px;color:#64748b;font-size:11px}.mini-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:10px}.mini{border:1px solid #e5e7eb;border-radius:12px;padding:14px;background:#fafbfc}.mini b{display:block;font-size:11px}.mini span{display:block;margin-top:6px;color:#64748b;font-size:10px}.bar{height:9px;background:#edf0f3;border-radius:99px;margin-top:9px;overflow:hidden}.fill{height:100%;background:#f59e0b;border-radius:99px}.notice{padding:11px 13px;border-radius:10px;background:#ecfdf3;color:#027a48;border:1px solid #abefc6;font-size:11px;font-weight:700}
@media(max-width:800px){.cp-an{padding:12px}.head{flex-direction:column;align-items:flex-start}.stats{grid-template-columns:1fr 1fr}.mini-grid{grid-template-columns:1fr}}@media(max-width:430px){.stats{grid-template-columns:1fr}}
</style>
@endpush
@section('content')
<div class="cp-an"><div class="cp-an-shell">
<section class="head"><div><div class="eyebrow">SELLER CENTER / PERFORMANCE</div><h1>Sales Analytics</h1><p>Track your vendor's order, sales and fulfillment performance.</p></div><a class="btn dark" href="{{route('vendor.dashboard')}}">← Seller Overview</a></section>
<div class="stats">
<div class="stat"><span>Total Orders</span><strong>{{$stats['orders']}}</strong><small>Orders containing your products</small></div>
<div class="stat money"><span>Paid Sales</span><strong>{{number_format((float)$stats['sales'],2)}}</strong><small>Paid order-item value</small></div>
<div class="stat"><span>Pending Orders</span><strong>{{$stats['pending']}}</strong><small>Orders requiring processing</small></div>
<div class="stat"><span>Delivered</span><strong>{{$stats['delivered']}}</strong><small>Completed fulfillment</small></div>
</div>
<section class="card"><h2>Performance snapshot</h2><p class="sub">A quick view of your current order pipeline.</p>
<div class="mini-grid">
<div class="mini"><b>Pending workload</b><span>{{$stats['pending']}} orders currently pending</span><div class="bar"><div class="fill" style="width:{{min(100,$stats['orders']?($stats['pending']/$stats['orders']*100):0)}}%"></div></div></div>
<div class="mini"><b>Delivery completion</b><span>{{$stats['delivered']}} delivered orders</span><div class="bar"><div class="fill" style="width:{{min(100,$stats['orders']?($stats['delivered']/$stats['orders']*100):0)}}%"></div></div></div>
<div class="mini"><b>Sales per order</b><span>{{number_format($stats['orders']?(float)$stats['sales']/$stats['orders']:0,2)}} average paid value</span><div class="bar"><div class="fill" style="width:{{min(100,$stats['sales']?50:0)}}%"></div></div></div>
</div></section>
<div class="notice">Analytics are calculated from this vendor's own order items and do not expose other sellers' data.</div>
</div></div>
@endsection