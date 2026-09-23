@extends('layouts.storefront')
@section('title','Sign In — Chacha Prime')
@section('content')
<div class="auth-shell"><div class="auth-card"><span class="eyebrow">SECURE ACCOUNT</span><h1>Welcome back</h1><p>Sign in with email/password, then complete email OTP and authenticator 2FA.</p>
@if(session('success'))<div class="notice success">{{ session('success') }}</div>@endif @if($errors->any())<div class="notice error">{{ $errors->first() }}</div>@endif
<form method="POST" action="{{ route('auth.login.submit') }}">@csrf<label>Email<input type="email" name="email" required></label><label>Password<input type="password" name="password" required></label><button class="button button-dark">Continue securely</button></form>
<p class="auth-foot">New to Chacha Prime? <a href="{{ route('auth.register') }}">Create an account</a></p></div></div>
@endsection