@extends('vendor.layout')
@section('title','Shipping & Fulfillment — Seller Center')
@push('styles')
<style>
.cp-ship{background:#f5f7fa;min-height:calc(100vh - 104px);padding:22px}.cp-ship-shell{max-width:1500px;margin:auto}.head,.card,.stat{background:#fff;border:1px solid #e5e7eb;border-radius:17px;box-shadow:0 7px 22px #11182706}.head{padding:21px 23px;display:flex;justify-content:space-between;align-items:center;gap:18px}.eyebrow{color:#d97706;font-size:10px;font-weight:900;letter-spacing:1.7px}.cp-ship h1{margin:5px 0;font-size:29px;letter-spacing:-.7px}.head p{margin:0;color:#64748b;font-size:12px}.btn{display:inline-flex;padding:10px 14px;border-radius:10px;text-decoration:none;font-size:12px;font-weight:800;border:1px solid #dbe1e8;color:#1f2937;background:#fff}.btn.dark{background:#111827;color:#fff}.stats{display:grid;grid-template-columns:repeat(3,1fr);gap:13px;margin:15px 0}.stat{padding:16px}.stat span{display:block;color:#64748b;font-size:10px;font-weight:800}.stat strong{display:block;font-size:25px;margin-top:7px}.stat small{color:#94a3b8;font-size:10px}.card{padding:19px;margin-bottom:15px}.card h2{margin:0;font-size:17px}.sub{margin:4px 0 16px;color:#64748b;font-size:11px}.ship-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:11px}.ship-item{border:1px solid #e5e7eb;border-radius:13px;padding:15px;background:#fafbfc}.ship-item h3{margin:0;font-size:13px}.ship-item p{margin:5px 0 11px;color:#64748b;font-size:10px}.badges{display:flex;gap:6px;flex-wrap:wrap}.badge{display:inline-flex;padding:5px 8px;border-radius:999px;font-size:9px;font-weight:900;text-transform:capitalize;background:#f1f5f9;color:#475569}.badge.on{background:#ecfdf3;color:#027a48}.info{padding:12px;background:#fffbeb;border:1px solid #fedf89;border-radius:11px;color:#92400e;font-size:10px;line-height:1.5}.empty{padding:25px;text-align:center;color:#64748b;font-size:11px}
@media(max-width:800px){.cp-ship{padding:12px}.head{flex-direction:column;align-items:flex-start}.stats{grid-template-columns:1fr}.ship-grid{grid-template-columns:1fr 1fr}}@media(max-width:520px){.ship-grid{grid-template-columns:1fr}}
</style>
@endpush
@section('content')
<div class="cp-ship"><div class="cp-ship-shell">
<section class="head"><div><div class="eyebrow">SELLER CENTER / FULFILLMENT</div><h1>Shipping & Fulfillment</h1><p>Review the shipping services currently available to marketplace sellers.</p></div><a class="btn dark" href="{{route('vendor.dashboard')}}">← Seller Overview</a></section>
<div class="stats">
<div class="stat"><span>Available Methods</span><strong>{{$methods->count()}}</strong><small>Enabled marketplace methods</small></div>
<div class="stat"><span>Manual Fulfillment</span><strong>{{$methods->where('mode','manual')->count()}}</strong><small>Methods handled by seller/admin</small></div>
<div class="stat"><span>Connected Providers</span><strong>{{$methods->where('provider','!=','manual')->unique('provider')->count()}}</strong><small>Configured external providers</small></div>
</div>
<section class="card"><h2>Available shipping methods</h2><p class="sub">These methods are enabled by marketplace administration and can be used for fulfillment.</p>
@if($methods->count())<div class="ship-grid">@foreach($methods as $m)<article class="ship-item"><h3>{{$m->name}}</h3><p>{{ucfirst($m->provider)}} shipping service</p><div class="badges"><span class="badge on">Enabled</span><span class="badge">{{ucfirst($m->mode)}}</span><span class="badge">{{ucfirst($m->provider)}}</span></div></article>@endforeach</div>
@else<div class="empty">No shipping methods are currently enabled. Contact marketplace administration to enable a method.</div>@endif
</section>
<section class="card"><h2>Fulfillment workflow</h2><p class="sub">Recommended seller workflow for every paid order.</p><div class="info"><strong>1. Confirm payment</strong> → <strong>2. Prepare & pack</strong> → <strong>3. Mark as processing</strong> → <strong>4. Dispatch / ship</strong> → <strong>5. Mark delivered</strong>. Order status can be updated from Seller Center → Orders.</div></section>
</div></div>
@endsection