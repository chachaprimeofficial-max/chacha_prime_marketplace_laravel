@extends('admin.layout')
@section('title','Marketplace Control Center — Chacha Prime')
@section('content')
<style>
.mp-wrap{max-width:1500px}.mp-head{display:flex;justify-content:space-between;gap:18px;align-items:flex-end;margin-bottom:18px}.mp-head h1{margin:0;font-size:28px}.mp-head p{margin:6px 0 0;color:#64748b}.mp-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px}.mp-card{background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:18px;box-shadow:0 8px 24px #0f172a08}.mp-card h2{font-size:17px;margin:0 0 14px}.mp-form{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:9px}.mp-form .full{grid-column:1/-1}.mp-form input,.mp-form select{width:100%;padding:10px;border:1px solid #cbd5e1;border-radius:8px;background:#fff}.mp-form button,.mp-btn{border:0;border-radius:8px;padding:10px 13px;background:#111827;color:#fff;font-weight:800;cursor:pointer}.mp-table{width:100%;border-collapse:collapse;font-size:11px}.mp-table th,.mp-table td{text-align:left;padding:9px 7px;border-bottom:1px solid #edf1f5;vertical-align:middle}.mp-table th{color:#64748b;font-size:9px;text-transform:uppercase;letter-spacing:.7px}.mp-on{color:#047857;font-weight:900}.mp-off{color:#b91c1c;font-weight:900}.mp-scroll{overflow:auto;max-height:410px}.mp-wide{grid-column:1/-1}@media(max-width:950px){.mp-grid{grid-template-columns:1fr}}@media(max-width:620px){.mp-form{grid-template-columns:1fr}.mp-form .full{grid-column:auto}.mp-head{display:block}}
</style>
<div class="mp-wrap">
 <div class="mp-head"><div><h1>Marketplace Control Center</h1><p>Countries, delivery zones, shipping rates and country-specific VAT/tax rules.</p></div><div style="font-size:11px;color:#64748b">30 marketplace countries configured</div></div>
 @if(session('success'))<div class="notice success">{{session('success')}}</div>@endif
 @if($errors->any())<div class="notice error">@foreach($errors->all() as $e)<div>{{$e}}</div>@endforeach</div>@endif

 <div class="mp-grid">
  <section class="mp-card mp-wide"><h2>Marketplace Countries</h2><div class="mp-scroll"><table class="mp-table"><thead><tr><th>Country</th><th>Code</th><th>Currency</th><th>Order</th><th>Status</th><th>Action</th></tr></thead><tbody>
  @foreach($countries as $c)<tr><form method="POST" action="{{route('admin.marketplaces.countries.update',$c->id)}}">@csrf<td><input name="name" value="{{e($c->name)}}" style="width:150px"></td><td><input name="code" value="{{e($c->code)}}" maxlength="2" style="width:65px"></td><td><input name="currency_code" value="{{e($c->currency_code)}}" maxlength="3" style="width:75px"></td><td><input type="number" name="sort_order" value="{{$c->sort_order}}" style="width:70px"></td><td><label><input type="checkbox" name="active" value="1" @checked($c->active)> Active</label></td><td><button class="mp-btn">Save</button></td></form></tr>@endforeach
  </tbody></table></div></section>

  <section class="mp-card"><h2>Create Shipping Zone</h2><form class="mp-form" method="POST" action="{{route('admin.marketplaces.zones.store')}}">@csrf
   <select name="country_id" required><option value="">Country</option>@foreach($countries as $c)<option value="{{$c->id}}">{{$c->name}} ({{$c->code}})</option>@endforeach</select>
   <select name="vendor_id"><option value="">All vendors</option>@foreach($vendors as $v)<option value="{{$v->id}}">{{$v->business_name}}</option>@endforeach</select>
   <input class="full" name="name" placeholder="Zone name e.g. Pakistan Standard Delivery" required><label><input type="checkbox" name="active" value="1" checked> Active</label><div><button class="mp-btn">Create zone</button></div>
  </form></section>

  <section class="mp-card"><h2>Create Shipping Method</h2><form class="mp-form" method="POST" action="{{route('admin.marketplaces.methods.store')}}">@csrf
   <select class="full" name="shipping_zone_id" required><option value="">Shipping zone</option>@foreach($zones as $z)<option value="{{$z->id}}">{{$z->country_code}} — {{$z->name}}{{ $z->business_name?' — '.$z->business_name:''}}</option>@endforeach</select>
   <input name="name" placeholder="Method name" required><input name="code" placeholder="Unique code" required>
   <select name="rate_type"><option value="flat">Flat rate</option><option value="weight">Weight based</option><option value="free">Free</option></select><input name="currency" value="USD" placeholder="Currency" required>
   <input type="number" step="0.01" name="base_rate" value="0" placeholder="Base rate" required><input type="number" step="0.01" name="per_kg_rate" value="0" placeholder="Per kg rate">
   <input type="number" step="0.01" name="free_over" placeholder="Free above order value"><input type="number" step="0.001" name="min_weight" placeholder="Min kg">
   <input type="number" step="0.001" name="max_weight" placeholder="Max kg"><input type="number" name="min_days" value="2" placeholder="Min days" required>
   <input type="number" name="max_days" value="7" placeholder="Max days" required><label><input type="checkbox" name="active" value="1" checked> Active</label>
   <div class="full"><button class="mp-btn">Create shipping method</button></div>
  </form></section>

  <section class="mp-card"><h2>Create Tax / VAT Rule</h2><form class="mp-form" method="POST" action="{{route('admin.marketplaces.tax.store')}}">@csrf
   <select name="country_id" required><option value="">Country</option>@foreach($countries as $c)<option value="{{$c->id}}">{{$c->name}} ({{$c->code}})</option>@endforeach</select>
   <input name="name" placeholder="Rule name e.g. Standard VAT" required><input type="number" step="0.0001" name="rate" placeholder="Tax rate %" required>
   <select name="vendor_id"><option value="">All vendors</option>@foreach($vendors as $v)<option value="{{$v->id}}">{{$v->business_name}}</option>@endforeach</select>
   <select name="category_id"><option value="">All categories</option>@foreach($categories as $cat)<option value="{{$cat->id}}">{{$cat->name}}</option>@endforeach</select>
   <label><input type="checkbox" name="prices_include_tax" value="1"> Prices include tax</label><label><input type="checkbox" name="active" value="1" checked> Active</label>
   <div class="full"><button class="mp-btn">Create tax rule</button></div>
  </form></section>

  <section class="mp-card mp-wide"><h2>Shipping Zones & Methods</h2><div class="mp-scroll"><table class="mp-table"><thead><tr><th>Country</th><th>Zone</th><th>Vendor</th><th>Method</th><th>Rate</th><th>Delivery</th><th>Status</th></tr></thead><tbody>@foreach($methods as $m)<tr><td>{{$m->country_code}}</td><td>{{$m->zone_name}}</td><td>{{$m->business_name??'All vendors'}}</td><td>{{$m->name}}</td><td>{{$m->rate_type}} / {{$m->currency}} {{$m->base_rate}}</td><td>{{$m->min_days}}–{{$m->max_days}} days</td><td>{{$m->active?'Active':'Disabled'}}</td></tr>@endforeach</tbody></table></div></section>

  <section class="mp-card mp-wide"><h2>Tax Rules</h2><div class="mp-scroll"><table class="mp-table"><thead><tr><th>Country</th><th>Rule</th><th>Rate</th><th>Vendor</th><th>Category</th><th>Prices include</th><th>Status</th></tr></thead><tbody>@foreach($taxRules as $t)<tr><td>{{$t->country_code}}</td><td>{{$t->name}}</td><td>{{$t->rate}}%</td><td>{{$t->vendor_id?'Vendor #'.$t->vendor_id:'All vendors'}}</td><td>{{$t->category_name??'All categories'}}</td><td>{{$t->prices_include_tax?'Yes':'No'}}</td><td>{{$t->active?'Active':'Disabled'}}</td></tr>@endforeach</tbody></table></div></section>
 </div>
</div>
@endsection