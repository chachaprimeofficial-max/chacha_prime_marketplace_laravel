@extends('layouts.customer')
@section('title','Address Book — Chacha Prime')
@push('styles')
<style>
.cp-addr-head{display:flex;justify-content:space-between;align-items:flex-end;gap:15px;margin-bottom:17px}.cp-addr-head p{margin:5px 0 0;color:#64748b;font-size:12px}.cp-addr-country{font-size:10px;color:#64748b;border:1px solid #dfe6ee;border-radius:10px;padding:9px 12px;background:#fff}.cp-addr-country strong{color:#0f172a}
.cp-addr-grid{display:grid;grid-template-columns:1.35fr .9fr;gap:15px}.cp-addr-card{background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:17px;box-shadow:0 8px 25px rgba(15,23,42,.05)}.cp-addr-card.default{border-color:#d39b36;box-shadow:0 8px 25px rgba(180,83,9,.08)}.cp-addr-top{display:flex;justify-content:space-between;gap:12px}.cp-addr-name{font-size:14px;font-weight:900}.cp-addr-label{display:inline-flex;margin-top:5px;padding:4px 7px;border-radius:999px;background:#f1f5f9;color:#475569;font-size:9px;font-weight:900}.cp-addr-default{background:#fffbeb;color:#a16207}.cp-addr-text{font-size:11px;line-height:1.65;color:#475569;margin-top:12px}.cp-addr-actions{display:flex;gap:7px;margin-top:13px}.cp-addr-btn{border:1px solid #cbd5e1;background:#fff;color:#0f172a;border-radius:8px;padding:8px 11px;font-size:10px;font-weight:900}.cp-addr-btn.primary{background:#0f172a;color:#fff;border-color:#0f172a}.cp-addr-empty{padding:35px;text-align:center;color:#64748b;font-size:11px;border:1px dashed #cbd5e1;border-radius:12px}.cp-addr-form{display:grid;grid-template-columns:1fr 1fr;gap:10px}.cp-addr-form label{display:grid;gap:5px;font-size:10px;font-weight:900;color:#475569}.cp-addr-form input,.cp-addr-form select{width:100%;padding:10px;border:1px solid #cbd5e1;border-radius:8px;font:inherit;font-size:12px}.cp-addr-form .full{grid-column:1/-1}.cp-addr-check{display:flex!important;align-items:center;display:flex;grid-template-columns:auto 1fr;gap:7px!important}.cp-addr-form button{grid-column:1/-1}.cp-addr-note{margin-top:10px;padding:10px;border-radius:8px;background:#f8fafc;color:#64748b;font-size:10px;line-height:1.55}
@media(max-width:900px){.cp-addr-grid{grid-template-columns:1fr}}@media(max-width:600px){.cp-addr-head{display:block}.cp-addr-country{display:inline-block;margin-top:10px}.cp-addr-form{grid-template-columns:1fr}.cp-addr-form .full,.cp-addr-form button{grid-column:1}.cp-addr-card{padding:14px}}
</style>
@endpush
@section('content')
<div class="cp-addr-head"><div><h1 class="cp-customer-page-title" style="margin-bottom:0">Address Book</h1><p>Manage delivery addresses for your selected marketplace country.</p></div><div class="cp-addr-country">Selected marketplace: <strong>{{strtoupper(session('marketplace_country_code','PK'))}}</strong></div></div>
@if(session('success'))<div class="notice success" style="margin-bottom:14px">{{session('success')}}</div>@endif
@if($errors->any())<div class="notice error" style="margin-bottom:14px">@foreach($errors->all() as $e)<div>{{$e}}</div>@endforeach</div>@endif
<div class="cp-addr-grid">
<section class="cp-addr-card"><div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px"><h2 style="margin:0;font-size:16px">Saved addresses</h2><span style="font-size:10px;color:#64748b">{{$addresses->count()}} saved</span></div>
@if($addresses->count())
<div style="display:grid;gap:10px">
@foreach($addresses as $a)
<article class="cp-addr-card {{$a->is_default?'default':''}}" style="box-shadow:none">
<div class="cp-addr-top"><div><div class="cp-addr-name">{{$a->recipient_name}}</div><span class="cp-addr-label {{$a->is_default?'cp-addr-default':''}}">{{$a->is_default?'Default address':($a->label??'Delivery address')}}</span></div><div style="font-size:10px;color:#64748b">{{$a->phone}}</div></div>
<div class="cp-addr-text">{{$a->address_line1}}@if($a->address_line2), {{$a->address_line2}}@endif<br>{{$a->city}}@if($a->state), {{$a->state}}@endif @if($a->postal_code), {{$a->postal_code}}@endif<br>{{$a->country}}</div>
<div class="cp-addr-actions"><button class="cp-addr-btn primary" type="button" disabled>Use at checkout</button><button class="cp-addr-btn" type="button" disabled>Edit</button></div>
</article>
@endforeach
</div>
@else<div class="cp-addr-empty">No saved delivery addresses. Add your first address to make checkout faster.</div>@endif
</section>
<section class="cp-addr-card"><h2 style="margin:0 0 4px;font-size:16px">Add address</h2><p style="margin:0 0 14px;color:#64748b;font-size:10px">Use the same country as the active marketplace when placing an order.</p>
<form class="cp-addr-form" method="POST" action="{{route('customer.addresses.store')}}">@csrf
<label>Label<input name="label" placeholder="Home, Office"></label><label>Recipient name<input name="recipient_name" required></label><label>Phone<input name="phone"></label><label>Country<input name="country" value="{{session('marketplace_country_code','PK')}}" required></label><label>State / province<input name="state"></label><label>City<input name="city"></label><label>Postal code<input name="postal_code"></label><label class="full">Address line 1<input name="address_line1" required></label><label class="full">Address line 2<input name="address_line2"></label><label class="cp-addr-check full"><input type="checkbox" name="is_default" value="1"> Make this my default address</label><button class="cp-customer-btn" type="submit">Save address</button></form><div class="cp-addr-note">For checkout, the address country must match the selected marketplace country. This protects shipping, tax and delivery calculations.</div>
</section>
</div>
@endsection