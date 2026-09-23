@extends('layouts.storefront')
@section('title','Reports — Seller Center')
@push('styles')
<style>
.r{background:#f5f7fa;min-height:calc(100vh - 104px);padding:22px}.s{max-width:1450px;margin:auto}.hero,.card{background:#fff;border:1px solid #e5e7eb;border-radius:18px;box-shadow:0 8px 24px #11182708}.hero{padding:22px;display:flex;justify-content:space-between;align-items:center;gap:18px}.ey{font-size:10px;font-weight:900;letter-spacing:1.8px;color:#d97706}.hero h1{margin:5px 0;font-size:30px}.muted{color:#64748b;font-size:12px}.btn{display:inline-flex;padding:10px 14px;border:1px solid #dbe1e8;border-radius:10px;text-decoration:none;color:#111827;font-size:12px;font-weight:800}.btn.dark{background:#111827;color:#fff}.card{padding:20px;margin-top:15px;overflow:auto}.table{width:100%;border-collapse:collapse;min-width:620px}.table th,.table td{padding:12px;border-bottom:1px solid #eef0f3;text-align:left;font-size:11px}.table th{font-size:9px;color:#64748b;text-transform:uppercase}@media(max-width:600px){.r{padding:12px}.hero{flex-direction:column;align-items:flex-start}}
</style>
@endpush
@section('content')
<div class="r"><div class="s"><section class="hero"><div><div class="ey">SELLER CENTER / REPORTING</div><h1>Business Reports</h1><p class="muted">Daily orders, units and paid sales for your vendor account.</p></div><div style="display:flex;gap:7px;flex-wrap:wrap">@foreach([7,30,90] as $range)<a class="btn {{$days===$range?'dark':''}}" href="{{route('vendor.reports',['days'=>$range])}}">{{$range}} Days</a>@endforeach<a class="btn" href="{{route('vendor.dashboard')}}">← Seller Overview</a></div></section>
<section class="card"><table class="table"><tr><th>Date</th><th>Orders</th><th>Units</th><th>Paid Sales</th></tr>@forelse($rows as $row)<tr><td>{{$row->day}}</td><td>{{$row->orders}}</td><td>{{$row->units}}</td><td>{{number_format((float)$row->sales,2)}}</td></tr>@empty<tr><td colspan="4">No report data for this period.</td></tr>@endforelse</table></section>
</div></div>
@endsection