@extends('layouts.storefront')
@section('title','Account Health — Seller Center')
@push('styles')
<style>
.h{background:#f5f7fa;min-height:calc(100vh - 104px);padding:22px}.s{max-width:1450px;margin:auto}.hero,.card,.m{background:#fff;border:1px solid #e5e7eb;border-radius:18px;box-shadow:0 8px 24px #11182708}.hero{padding:22px;display:flex;justify-content:space-between;align-items:center;gap:18px}.ey{font-size:10px;font-weight:900;letter-spacing:1.8px;color:#d97706}.hero h1{margin:5px 0;font-size:30px}.muted{color:#64748b;font-size:12px}.btn{display:inline-flex;padding:10px 14px;border:1px solid #dbe1e8;border-radius:10px;text-decoration:none;color:#111827;font-size:12px;font-weight:800}.btn.dark{background:#111827;color:#fff}.ms{display:grid;grid-template-columns:repeat(4,1fr);gap:13px;margin:15px 0}.m{padding:16px}.m span{display:block;color:#64748b;font-size:10px;font-weight:800}.m strong{display:block;font-size:25px;margin-top:7px}.card{padding:20px;margin-bottom:15px}.card h2{margin:0;font-size:17px}.grid{display:grid;grid-template-columns:repeat(2,1fr);gap:12px;margin-top:15px}.item{border:1px solid #e5e7eb;border-radius:13px;padding:15px}.item b{font-size:12px}.item p{font-size:10px;color:#64748b;margin:6px 0}.bar{height:8px;background:#eef2f6;border-radius:99px;overflow:hidden}.bar i{display:block;height:100%;background:#f59e0b}.note{padding:12px;background:#fffbeb;border:1px solid #fedf89;border-radius:10px;font-size:11px;color:#92400e}@media(max-width:700px){.h{padding:12px}.hero{flex-direction:column;align-items:flex-start}.ms,.grid{grid-template-columns:1fr 1fr}}@media(max-width:450px){.ms,.grid{grid-template-columns:1fr}}
</style>
@endpush
@section('content')
<div class="h"><div class="s"><section class="hero"><div><div class="ey">SELLER CENTER / ACCOUNT HEALTH</div><h1>Account Health</h1><p class="muted">Operational indicators for your seller account.</p></div><a class="btn dark" href="{{route('vendor.dashboard')}}">← Seller Overview</a></section>
<div class="ms"><div class="m"><span>Total Products</span><strong>{{$totalProducts}}</strong></div><div class="m"><span>Published</span><strong>{{$published}}</strong></div><div class="m"><span>Low Stock</span><strong>{{$lowStock}}</strong></div><div class="m"><span>Reviews</span><strong>{{$reviewCount}}</strong></div></div>
<section class="card"><h2>Performance indicators</h2><div class="grid">
<div class="item"><b>Catalog approval</b><p>{{$published}} published of {{$totalProducts}} total</p><div class="bar"><i style="width:{{min(100,$totalProducts?($published/$totalProducts*100):0)}}%"></i></div></div>
<div class="item"><b>Fulfillment completion</b><p>{{$delivered}} delivered of {{$totalOrders}} orders</p><div class="bar"><i style="width:{{min(100,$totalOrders?($delivered/$totalOrders*100):0)}}%"></i></div></div>
<div class="item"><b>Cancellation rate</b><p>{{$cancelled}} cancelled of {{$totalOrders}} orders</p><div class="bar"><i style="width:{{min(100,$totalOrders?($cancelled/$totalOrders*100):0)}}%"></i></div></div>
<div class="item"><b>Customer rating</b><p>{{number_format($avgRating,2)}} / 5 average rating</p><div class="bar"><i style="width:{{min(100,$avgRating*20)}}%"></i></div></div>
</div></section>
<div class="note">These are operational metrics calculated from your catalog, orders and reviews. They are not an official marketplace policy score.</div>
</div></div>
@endsection