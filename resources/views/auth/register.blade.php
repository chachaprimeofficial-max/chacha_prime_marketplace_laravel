@extends('layouts.storefront')
@section('title', 'Create Account — Chacha Prime')
@section('content')
<div class="auth-shell"><div class="auth-card"><span class="eyebrow">JOIN CHACHA PRIME</span><h1>Create your account</h1><p>Choose the account type that fits how you want to use the marketplace.</p><div class="role-grid"><a href="#">Customer</a><a href="#">B2B Buyer</a><a href="{{ route('vendor.register') }}">Seller / Vendor</a><a href="#">Affiliate / Partner</a></div></div></div>
@endsection
