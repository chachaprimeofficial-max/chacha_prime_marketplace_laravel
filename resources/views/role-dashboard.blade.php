@extends('layouts.storefront')
@section('title',$role.' — Chacha Prime')
@push('styles')
<style>
.rd{min-height:calc(100vh - 104px);background:#f5f7fa;padding:24px}.shell{max-width:1300px;margin:auto}.hero,.stat,.card,.action{background:#fff;border:1px solid #e5e7eb;border-radius:18px;box-shadow:0 8px 25px #11182708}.hero{padding:25px;display:flex;justify-content:space-between;align-items:center;gap:18px}.ey{font-size:10px;font-weight:900;letter-spacing:1.8px;color:#d97706}.hero h1{font-size:30px;margin:6px 0}.muted{color:#64748b;font-size:12px}.btn{display:inline-flex;padding:10px 14px;border-radius:10px;background:#111827;color:#fff;text-decoration:none;font-size:12px;font-weight:800}.stats{display:grid;grid-template-columns:repeat(3,1fr);gap:13px;margin:15px 0}.stat{padding:18px}.stat span{display:block;color:#64748b;font-size:10px;font-weight:800}.stat strong{display:block;font-size:25px;margin-top:8px}.card{padding:20px}.card h2{margin:0;font-size:18px}.actions{display:grid;grid-template-columns:repeat(4,1fr);gap:11px;margin-top:14px}.action{padding:16px;text-decoration:none;color:#111827}.action b{font-size:13px}.action span{display:block;color:#64748b;font-size:10px;margin-top:5px}@media(max-width:750px){.rd{padding:12px}.hero{flex-direction:column;align-items:flex-start}.stats{grid-template-columns:1fr}.actions{grid-template-columns:1fr 1fr}}@media(max-width:430px){.actions{grid-template-columns:1fr}}
</style>
@endpush
@section('content')
<div class="rd"><div class="shell"><section class="hero"><div><div class="ey">{{$eyebrow}}</div><h1>{{$role}}</h1><p class="muted">{{$intro}}</p></div><a class="btn" href="{{route('home')}}">Open Storefront →</a></section>
<section class="stats">@foreach($stats as $s)<div class="stat"><span>{{$s[0]}}</span><strong>{{$s[1]}}</strong><small class="muted">{{$s[2]}}</small></div>@endforeach</section>
<section class="card"><h2>Quick access</h2><div class="actions">@foreach($actions as $a)<a class="action" href="{{$a[2]}}"><b>{{$a[0]}}</b><span>{{$a[1]}}</span></a>@endforeach</div></section>
</div></div>
@endsection