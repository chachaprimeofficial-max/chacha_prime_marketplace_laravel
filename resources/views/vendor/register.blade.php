@extends('layouts.storefront')
@section('title', 'Seller Registration — Chacha Prime')
@section('content')
<div class="auth-shell"><div class="auth-card wide"><span class="eyebrow">SELL ON CHACHA PRIME</span><h1>Vendor registration</h1><p>Seller onboarding will include business details, identity verification, payout setup and marketplace compliance.</p><form><div class="form-grid"><label>Business name<input type="text" name="business_name"></label><label>Business email<input type="email" name="email"></label><label>Country<input type="text" name="country"></label><label>Business type<input type="text" name="business_type"></label></div><button class="button button-dark" type="submit">Start verification</button></form></div></div>
@endsection
