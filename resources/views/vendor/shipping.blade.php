@extends('vendor.layout')
@section('title','Shipping Management')
@push('styles')<style>
.ship{max-width:1400px}.ship h1{margin:0;font-size:26px}.sub{color:#64748b;font-size:12px;margin:5px 0 18px}.grid{display:grid;grid-template-columns:1fr 1fr;gap:16px}.card{background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:18px;margin-bottom:16px}.card h2{font-size:16px;margin:0 0 14px}.form{display:grid;grid-template-columns:1fr 1fr;gap:9px}.form .full{grid-column:1/-1}.form input,.form select{width:100%;box-sizing:border-box;padding:10px;border:1px solid #cbd5e1;border-radius:8px}.form button,.act{border:0;border-radius:8px;background:#111827;color:#fff;padding:10px 13px;font-weight:800}.table{width:100%;border-collapse:collapse;font-size:11px}.table th,.table td{padding:9px 7px;border-bottom:1px solid #edf1f5;text-align:left}.table th{font-size:9px;color:#64748b;text-transform:uppercase}.scroll{overflow:auto}@media(max-width:850px){.grid{grid-template-columns:1fr}}@media(max-width:600px){.form{grid-template-columns:1fr}.form .full{grid-column:auto}}
</style>@endpush
@section('content')
<div class="ship"><h1>Shipping Management</h1><div class="sub">Manage your country-specific delivery zones and rates. Marketplace-wide rules remain controlled by Admin.</div>
@if(session('success'))<div class="notice success">{{session('success')}}</div>@endif
@if($errors->any())<div class="notice error">@foreach($errors->all() as $e)<div>{{$e}}</div>@endforeach</div>@endif
<div class="grid">
<section class="card"><h2>Create Seller Shipping Zone</h2><form class="form" method="POST" action="{{route('vendor.shipping.zones.store')}}">@csrf
<select name="country_id" required><option value="">Marketplace country</option>@foreach($countries as $c)<option value="{{$c->id}}">{{$c->name}} ({{$c->code}})</option>@endforeach</select><input name="name" placeholder="Zone name" required>
<label class="full"><input type="checkbox" name="active" value="1" checked> Active</label><div class="full"><button>Create zone</button></div></form></section>
<section class="card"><h2>Create Shipping Method</h2><form class="form" method="POST" action="{{route('vendor.shipping.methods.store')}}">@csrf
<select class="full" name="shipping_zone_id" required><option value="">Your shipping zone</option>@foreach($zones->where('vendor_id',$vendor->id) as $z)<option value="{{$z->id}}">{{$z->name}}</option>@endforeach</select>
<input name="name" placeholder="Method name" required><input name="code" placeholder="Unique code" required><select name="rate_type"><option value="flat">Flat rate</option><option value="weight">Weight based</option><option value="free">Free</option></select><input name="currency" value="USD" required>
<input type="number" step="0.01" name="base_rate" value="0" placeholder="Base rate" required><input type="number" step="0.01" name="per_kg_rate" value="0" placeholder="Per kg rate"><input type="number" step="0.01" name="free_over" placeholder="Free above order value"><input type="number" step="0.001" name="min_weight" placeholder="Minimum kg"><input type="number" step="0.001" name="max_weight" placeholder="Maximum kg"><input type="number" name="min_days" value="2" required><input type="number" name="max_days" value="7" required><label><input type="checkbox" name="active" value="1" checked> Active</label>
<div class="full"><button>Create shipping method</button></div></form></section>
</div>
<section class="card"><h2>Your Shipping Methods</h2><div class="scroll"><table class="table"><thead><tr><th>Country</th><th>Zone</th><th>Method</th><th>Rate</th><th>Delivery</th><th>Status</th><th></th></tr></thead><tbody>
@forelse($methods as $m)<tr><td>{{$m->country_code}}</td><td>{{$m->zone_name}}</td><td>{{$m->name}}</td><td>{{$m->rate_type}} / {{$m->currency}} {{$m->base_rate}}</td><td>{{$m->min_days}}–{{$m->max_days}} days</td><td>{{$m->active?'Active':'Disabled'}}</td><td>@if($m->vendor_id==$vendor->id)<form method="POST" action="{{route('vendor.shipping.methods.toggle',$m->id)}}">@csrf<button class="act">{{$m->active?'Disable':'Enable'}}</button></form>@endif</td></tr>@empty<tr><td colspan="7">No shipping methods configured yet.</td></tr>@endforelse
</tbody></table></div></section>
</div>@endsection