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
<h2>CSV / Feed Import</h2><p style="color:#64748b;font-size:11px">Foundation for bulk catalog import. CSV import is live. Required columns: name, vendor_id; optional: category_id, brand_id, sku, retail_price, cost_price, currency, stock, description.</p>
<form method="POST" action="{{route('admin.catalog-import')}}" enctype="multipart/form-data">@csrf
<input type="file" name="file" accept=".csv,.txt" required>
<button style="margin-top:10px;padding:10px 14px;border:0;border-radius:9px;background:#111827;color:#fff">Import CSV</button>
</form>
<div style="margin-top:18px;padding:12px;background:#f8fafc;border-radius:10px;font-size:11px">Planned mapping: SKU → Product ID → Category → Brand → prices → stock → images → attributes. Duplicate SKU detection and row-level error reports will be included.</div>
</section>
<section class="panel">
<h2>AI Product Builder</h2><form id="aiProductForm" style="display:grid;gap:8px"><textarea id="aiInput" rows="4" placeholder="Example: Samsung Galaxy A55 5G 256GB Black..."></textarea><button type="submit" style="padding:10px;border:0;border-radius:9px;background:#111827;color:#fff">Generate Product Content</button></form><pre id="aiResult" style="white-space:pre-wrap;font-size:10px;max-height:180px;overflow:auto"></pre>
</section><section class="panel"><h2>AI Category Builder</h2><form id="catAi" style="display:grid;gap:8px"><textarea id="catInput" rows="3" placeholder="Example: Mobile Phones & Accessories"></textarea><button style="padding:10px;border:0;border-radius:9px;background:#111827;color:#fff">Generate Preview</button></form><div id="catEditor" style="display:none;margin-top:12px;gap:7px"><input id="catName" placeholder="Category name"><input id="catParent" placeholder="Parent category ID (optional)"><input id="catSeo" placeholder="SEO title"><input id="catSeoDesc" placeholder="SEO description"><textarea id="catDesc" rows="3" placeholder="Description"></textarea><textarea id="catAttrs" rows="5" placeholder='Attributes JSON'></textarea><button id="catSave" type="button" style="padding:10px;border:0;border-radius:9px;background:#0f766e;color:#fff">Approve & Save Category</button></div><pre id="catOut" style="white-space:pre-wrap;font-size:10px;max-height:160px;overflow:auto"></pre></section><section class="panel">
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
<script>document.getElementById("aiProductForm")?.addEventListener("submit",async e=>{e.preventDefault();let out=document.getElementById("aiResult");out.textContent="Generating...";let r=await fetch("{{route("admin.ai-product-builder")}}",{method:"POST",headers:{"Content-Type":"application/json","X-CSRF-TOKEN":"{{csrf_token()}}"},body:JSON.stringify({input:document.getElementById("aiInput").value})});let j=await r.json();out.textContent=JSON.stringify(j.data||j.raw||j,null,2)});const catForm=document.getElementById("catAi");catForm?.addEventListener("submit",async e=>{e.preventDefault();let o=document.getElementById("catOut");o.textContent="Generating preview...";let r=await fetch("{{route("admin.ai-category-builder")}}",{method:"POST",headers:{"Content-Type":"application/json","X-CSRF-TOKEN":"{{csrf_token()}}"},body:JSON.stringify({input:document.getElementById("catInput").value})});let j=await r.json(),d=j.data||{};document.getElementById("catEditor").style.display="grid";document.getElementById("catName").value=d.category_name||"";document.getElementById("catParent").value=d.parent_id||"";document.getElementById("catSeo").value=d.seo_title||"";document.getElementById("catSeoDesc").value=d.seo_description||"";document.getElementById("catDesc").value=d.description||"";document.getElementById("catAttrs").value=JSON.stringify(d.attributes||[],null,2);o.textContent=JSON.stringify(d,null,2)});document.getElementById("catSave")?.addEventListener("click",async()=>{let r=await fetch("{{route("admin.ai-category-builder.save")}}",{method:"POST",headers:{"Content-Type":"application/json","X-CSRF-TOKEN":"{{csrf_token()}}"},body:JSON.stringify({name:document.getElementById("catName").value,parent_id:document.getElementById("catParent").value||null,seo_title:document.getElementById("catSeo").value,seo_description:document.getElementById("catSeoDesc").value,description:document.getElementById("catDesc").value,attributes:document.getElementById("catAttrs").value})});if(r.ok){location.reload()}else{let j=await r.json();alert(j.message||"Could not save category")}});</script>
@endsection