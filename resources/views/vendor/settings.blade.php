@extends('vendor.layout')
@section('title','Seller Account Settings — Chacha Prime')
@push('styles')<style>
.cp-set{background:#f5f7fa;min-height:calc(100vh - 104px);padding:22px}.cp-set-shell{max-width:1100px;margin:auto}.cp-set-head,.cp-set-card{background:#fff;border:1px solid #e5e7eb;border-radius:17px;box-shadow:0 8px 25px #11182706}.cp-set-head{padding:21px 23px;margin-bottom:15px}.cp-set-head h1{margin:5px 0;font-size:28px}.cp-set-head p{margin:0;color:#64748b;font-size:12px}.cp-set-card{padding:20px}.cp-set-form{display:grid;grid-template-columns:1fr 1fr;gap:12px}.cp-set-form label{display:grid;gap:5px;color:#475569;font-size:10px;font-weight:900}.cp-set-form input,.cp-set-form textarea{border:1px solid #dbe1e8;border-radius:9px;padding:10px;font:inherit;font-size:12px}.cp-set-form .full,.cp-set-form button{grid-column:1/-1}.cp-set-form textarea{min-height:110px}.cp-set-form button{border:0;border-radius:10px;background:#111827;color:#fff;padding:12px;font-weight:900}.cp-set-notice{margin-bottom:13px;padding:11px;border-radius:9px;background:#ecfdf3;color:#027a48;font-size:11px;font-weight:800}@media(max-width:650px){.cp-set{padding:12px}.cp-set-form{grid-template-columns:1fr}.cp-set-form .full,.cp-set-form button{grid-column:1}}
</style>@endpush
@section('content')
<div class="cp-set"><div class="cp-set-shell"><section class="cp-set-head"><div style="font-size:10px;font-weight:900;letter-spacing:1.6px;color:#d97706">SELLER CENTER / ACCOUNT</div><h1>Seller account settings</h1><p>Manage the legal and storefront information used across your marketplace account.</p></section>
@if(session('success'))<div class="cp-set-notice">{{session('success')}}</div>@endif
@if($errors->any())<div class="cp-set-notice" style="background:#fef3f2;color:#b42318">@foreach($errors->all() as $error)<div>{{$error}}</div>@endforeach</div>@endif
<section class="cp-set-card"><form class="cp-set-form" method="POST" action="{{route('vendor.settings.update')}}">@csrf
<label>Business name<input name="business_name" value="{{$vendor->business_name}}" required></label>
<label>Legal name<input name="legal_name" value="{{$vendor->legal_name}}"></label>
<label>Seller country<input name="country" value="{{$vendor->country}}"></label>
<label>Registration number<input name="registration_number" value="{{$vendor->registration_number}}"></label>
<label>Tax number<input name="tax_number" value="{{$vendor->tax_number}}"></label>
<label class="full">Business description<textarea name="description">{{$vendor->description}}</textarea></label>
<button type="submit">Save seller settings</button></form></section></div></div>
@endsection