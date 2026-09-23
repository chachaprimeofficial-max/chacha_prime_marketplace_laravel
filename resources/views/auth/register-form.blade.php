@extends('layouts.storefront')
@section('title','Create Account — Chacha Prime')
@section('content')
<section class="auth-shell"><div class="auth-card"><span class="eyebrow">CHACHA PRIME</span><h1>Create account</h1><p>Choose your marketplace account type.</p>
@if($errors->any())<div class="notice error">@foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach</div>@endif
<form method="POST" action="{{ route('auth.register.submit') }}">@csrf
<label>Full name<input name="name" value="{{ old('name') }}" required></label>
<label>Email<input type="email" name="email" value="{{ old('email') }}" required></label>
<label>Phone<input name="phone" value="{{ old('phone') }}"></label>
<label>Account type<select name="type" required style="padding:14px;border:1px solid #dfe2e7;border-radius:12px"><option value="customer">B2C Customer</option><option value="b2b_customer">B2B Customer</option><option value="vendor" @selected(request('type')==='vendor')>Vendor / Seller</option></select></label>
<label>Password<input type="password" name="password" required minlength="8"></label>
<label>Confirm password<input type="password" name="password_confirmation" required minlength="8"></label>
<button class="button button-dark" type="submit">Create account</button></form><div class="auth-foot">Already registered? <a href="{{ route('auth.login') }}"><b>Sign in</b></a></div></div></section>
@endsection