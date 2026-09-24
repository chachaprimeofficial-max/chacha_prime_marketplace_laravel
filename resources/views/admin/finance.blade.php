@extends('admin.layout')
@section('title','Finance & Settlements — Chacha Prime')
@section('page_heading','Finance & Settlements')
@section('content')
<style>
.finance-grid{display:grid;grid-template-columns:repeat(6,minmax(0,1fr));gap:12px;margin-bottom:18px}
.finance-card{background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:16px;box-shadow:0 8px 24px rgba(15,23,42,.04)}
.finance-card small{display:block;color:#64748b;font-size:11px;text-transform:uppercase;letter-spacing:.08em}.finance-card strong{display:block;margin-top:7px;font-size:22px;color:#0f172a}
.finance-layout{display:grid;grid-template-columns:1fr 1.5fr;gap:18px}.panel{min-width:0}
.finance-toolbar{display:flex;gap:8px;align-items:center;justify-content:space-between;flex-wrap:wrap;margin-bottom:14px}
.finance-toolbar form,.inline-form{display:flex;gap:7px;align-items:center;flex-wrap:wrap}
.finance-toolbar select,.finance-toolbar button,.inline-form input,.inline-form button{min-height:36px}
.status{display:inline-flex;padding:5px 9px;border-radius:999px;background:#f1f5f9;color:#334155;font-size:11px;font-weight:700}
.table-wrap{overflow:auto}.table-wrap table{width:100%;min-width:720px;border-collapse:collapse}.table-wrap th,.table-wrap td{padding:11px 10px;border-bottom:1px solid #eef2f7;text-align:left;vertical-align:middle;font-size:12px}.table-wrap th{font-size:10px;text-transform:uppercase;letter-spacing:.06em;color:#64748b}
.muted{color:#64748b;font-size:11px}.button{border:0;border-radius:9px;padding:8px 11px;font-weight:700;cursor:pointer}.button-dark{background:#0f172a;color:#fff}.button-light{background:#f1f5f9;color:#0f172a}
@media(max-width:1100px){.finance-grid{grid-template-columns:repeat(3,1fr)}.finance-layout{grid-template-columns:1fr}}
@media(max-width:650px){.finance-grid{grid-template-columns:repeat(2,1fr)}.finance-card strong{font-size:18px}}
</style>
@if(session('success'))<div class="notice">{{session('success')}}</div>@endif
@if($errors->any())<div class="notice" style="background:#fff1f2;color:#9f1239">{{implode(' ', $errors->all())}}</div>@endif

<div class="finance-grid">
 <div class="finance-card"><small>Gross seller sales</small><strong>{{number_format((float)$gross,2)}}</strong></div>
 <div class="finance-card"><small>Marketplace commission</small><strong>{{number_format((float)$commission,2)}}</strong></div>
 <div class="finance-card"><small>Pending settlement</small><strong>{{number_format((float)$pending,2)}}</strong></div>
 <div class="finance-card"><small>Eligible settlement</small><strong>{{number_format((float)$eligible,2)}}</strong></div>
 <div class="finance-card"><small>Settled earnings</small><strong>{{number_format((float)$settled,2)}}</strong></div>
 <div class="finance-card"><small>Refunded / cancelled</small><strong>{{number_format((float)$refunded,2)}}</strong></div>
</div>

<div class="finance-layout">
 <div class="panel">
  <div class="section-title" style="margin-top:0"><div><h2>Seller commission</h2><p>Set the commission rate used for future settlement calculations.</p></div></div>
  <div class="table-wrap"><table><thead><tr><th>Seller</th><th>Current</th><th>Update</th></tr></thead><tbody>
  @forelse($vendors as $v)<tr>
   <td><strong>{{e($v->business_name)}}</strong><br><span class="muted">{{e($v->user_name)}}</span></td>
   <td><span class="status">{{number_format((float)$v->commission_rate,2)}}%</span></td>
   <td><form class="inline-form" method="POST" action="{{route('admin.finance.vendors.commission',$v->id)}}">@csrf<input type="number" name="commission_rate" value="{{number_format((float)$v->commission_rate,2,'.','')}}" min="0" max="100" step="0.01" style="width:92px"><button class="button button-dark" type="submit">Save</button></form></td>
  </tr>@empty<tr><td colspan="3">No sellers found.</td></tr>@endforelse
  </tbody></table></div>
 </div>

 <div class="panel">
  <div class="finance-toolbar">
   <div><h2 style="margin:0">Settlement ledger</h2><p class="muted" style="margin:4px 0 0">Delivered orders become eligible after the 7-day return window.</p></div>
   <div style="display:flex;gap:7px;flex-wrap:wrap">
    <form method="GET"><select name="status"><option value="">All statuses</option>@foreach(['pending','eligible','settled','refunded','cancelled'] as $s)<option value="{{$s}}" @selected($status===$s)>{{$s}}</option>@endforeach</select><select name="vendor_id"><option value="">All sellers</option>@foreach($vendors as $v)<option value="{{$v->id}}" @selected((string)$vendorId===(string)$v->id)>{{e($v->business_name)}}</option>@endforeach</select><button class="button button-light" type="submit">Filter</button></form>
    <form method="POST" action="{{route('admin.finance.settlements.refresh')}}">@csrf<button class="button button-dark" type="submit">Refresh eligibility</button></form>
   </div>
  </div>
  <div class="table-wrap"><table><thead><tr><th>Seller / Order</th><th>Item</th><th>Gross</th><th>Commission</th><th>Net</th><th>Eligible</th><th>Status</th><th>Action</th></tr></thead><tbody>
  @forelse($settlements as $s)<tr>
   <td><strong>{{e($s->business_name)}}</strong><br><span class="muted">{{e($s->order_number)}}</span></td>
   <td>{{e($s->product_name)}}</td>
   <td>{{e($s->currency)}} {{number_format((float)$s->gross_amount,2)}}</td>
   <td>{{number_format((float)$s->commission_amount,2)}}</td>
   <td><strong>{{number_format((float)$s->net_amount,2)}}</strong></td>
   <td class="muted">{{$s->eligible_at ?: '—'}}</td>
   <td><span class="status">{{$s->status}}</span></td>
   <td>@if(in_array($s->status,['pending','eligible'],true))<form method="POST" action="{{route('admin.finance.settlements.release',$s->id)}}">@csrf<button class="button button-dark" type="submit">Release</button></form>@else<span class="muted">Finalized</span>@endif</td>
  </tr>@empty<tr><td colspan="8">No settlement records found.</td></tr>@endforelse
  </tbody></table></div>
  <div style="margin-top:12px">{{$settlements->links()}}</div>
 </div>
</div>
@endsection
