@extends('admin.layout')
@section('title','Catalog Tools — Chacha Prime')
@section('page_heading','Catalog Intelligence & Operations')
@section('content')
@if(session('success'))<div class="notice">{{session('success')}}</div>@endif
<div class="admin-grid">
<div class="admin-stat"><span class="label">Product Identifiers</span><strong>{{$identifierCount}}</strong><small>Internal product IDs / SKUs / barcodes</small></div>
<div class="admin-stat"><span class="label">Import Jobs</span><strong>{{$imports->total()}}</strong><small>CSV and future feed/sync jobs</small></div>
<div class="admin-stat"><span class="label">Subscription Plans</span><strong>{{$subscriptionPlans}}</strong><small>Vendor / B2B / customer plans</small></div>
<div class="admin-stat"><span class="label">AI Catalog</span><strong>Ready</strong><small>Connects to existing Gemini service</small></div>
</div>
<div class="dashboard-two">
<section class="panel">
<h2>CSV / Feed Import</h2><p style="color:#64748b;font-size:11px">Foundation for bulk catalog import. Processing can be moved to Laravel queues for large files.</p>
<form method="POST" action="#" enctype="multipart/form-data">@csrf
<input type="file" name="file" accept=".csv,.xlsx,.xml,.json" disabled>
<button disabled style="margin-top:10px;padding:10px 14px;border:0;border-radius:9px;background:#cbd5e1">Import engine next</button>
</form>
<div style="margin-top:18px;padding:12px;background:#f8fafc;border-radius:10px;font-size:11px">Planned mapping: SKU → Product ID → Category → Brand → prices → stock → images → attributes. Duplicate SKU detection and row-level error reports will be included.</div>
</section>
<section class="panel">
<h2>Subscription Plans</h2>
<form method="POST" action="{{route('admin.subscription-plans.store')}}" style="display:grid;gap:8px">@csrf
<input name="name" placeholder="Plan name" required><select name="audience"><option value="vendor">Vendor</option><option value="b2b_customer">B2B Customer</option><option value="customer">Customer</option></select>
<input name="price" type="number" step="0.01" value="0" placeholder="Price"><input name="currency" value="USD" maxlength="3"><select name="billing_cycle"><option>monthly</option><option>yearly</option></select>
<input name="product_limit" type="number" placeholder="Product limit"><input name="ai_limit" type="number" placeholder="AI usage limit"><input name="import_limit" type="number" placeholder="Import limit">
<button style="padding:10px;border:0;border-radius:9px;background:#111827;color:white">Create Plan</button>
</form>
</section>
</div>
<div class="panel" style="margin-top:14px">
<h2>Import History</h2><div class="table-wrap"><table><thead><tr><th>ID</th><th>Source</th><th>Status</th><th>Rows</th><th>Imported</th><th>Failed</th></tr></thead><tbody>
@forelse($imports as $i)<tr><td>#{{$i->id}}</td><td>{{$i->source_type}}</td><td><span class="status">{{$i->status}}</span></td><td>{{$i->total_rows}}</td><td>{{$i->imported_rows}}</td><td>{{$i->failed_rows}}</td></tr>@empty<tr><td colspan="6">No imports yet.</td></tr>@endforelse
</tbody></table></div>{{$imports->links()}}</div>
@endsection