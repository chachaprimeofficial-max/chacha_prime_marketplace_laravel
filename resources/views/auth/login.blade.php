@extends('layouts.storefront')
@section('title','Sign In — Chacha Prime')
@push('styles')
<style>
.auth-page{min-height:calc(100vh - 104px);background:linear-gradient(135deg,#f5f7fb,#eef2f7);display:grid;place-items:center;padding:35px 16px}.auth-wrap{width:min(980px,100%);display:grid;grid-template-columns:1fr 1fr;background:#fff;border:1px solid #e5e7eb;border-radius:24px;overflow:hidden;box-shadow:0 20px 60px #0f172a14}.auth-brand{background:#0b1220;color:#fff;padding:38px;display:flex;flex-direction:column;justify-content:center}.auth-brand img{width:210px;max-width:100%;height:58px;object-fit:contain;object-position:left}.auth-brand h2{font-size:30px;margin:28px 0 10px;letter-spacing:-.8px}.auth-brand p{color:#cbd5e1;font-size:13px;line-height:1.7}.auth-points{display:grid;gap:10px;margin-top:22px}.auth-point{padding:11px 13px;border:1px solid #ffffff14;background:#ffffff08;border-radius:11px;font-size:11px;color:#e2e8f0}.auth-form{padding:38px}.eyebrow{font-size:10px;font-weight:900;letter-spacing:1.8px;color:#d97706}.auth-form h1{font-size:30px;margin:7px 0}.lead{color:#64748b;font-size:12px;line-height:1.6}.notice{padding:11px 13px;border-radius:10px;font-size:11px;margin:14px 0}.success{background:#ecfdf3;color:#027a48;border:1px solid #abefc6}.error{background:#fef3f2;color:#b42318;border:1px solid #fecdca}.auth-form form{display:grid;gap:13px;margin-top:20px}.field{display:grid;gap:6px;font-size:11px;font-weight:800;color:#334155}.field input{padding:13px;border:1px solid #dbe1e8;border-radius:11px;font-size:13px;outline:none}.field input:focus{border-color:#f59e0b;box-shadow:0 0 0 3px #f59e0b1a}.button{border:0;border-radius:11px;padding:13px;font-weight:900;cursor:pointer}.button-dark{background:#111827;color:#fff}.auth-foot{text-align:center;color:#64748b;font-size:12px;margin-top:18px}.auth-foot a{color:#b45309;font-weight:900;text-decoration:none}@media(max-width:750px){.auth-wrap{grid-template-columns:1fr}.auth-brand{display:none}.auth-form{padding:25px}}
</style>
@endpush
@section('content')
<section class="auth-page"><div class="auth-wrap">
<div class="auth-brand"><img src="{{asset('images/chacha-logo.svg')}}" alt="CHACHA 查查 Prime"><h2>Welcome back.</h2><p>Access your Chacha Prime marketplace account securely.</p><div class="auth-points"><div class="auth-point">✓ Email verification code</div><div class="auth-point">✓ Google Authenticator / TOTP security</div><div class="auth-point">✓ Role-based dashboard access</div></div></div>
<div class="auth-form"><span class="eyebrow">SECURE ACCOUNT ACCESS</span><h1>Sign in</h1><p class="lead">Enter your account credentials. We will then verify your email OTP and authenticator code.</p>
@if(session('success'))<div class="notice success">{{session('success')}}</div>@endif
@if($errors->any())<div class="notice error">{{$errors->first()}}</div>@endif
<form method="POST" action="{{route('auth.login.submit')}}">@csrf
<label class="field">Email<input type="email" name="email" value="{{old('email')}}" autocomplete="email" required></label>
<label class="field">Password<input type="password" name="password" autocomplete="current-password" required></label>
<button class="button button-dark" type="submit">Continue securely →</button>
</form>
<p class="auth-foot">New to Chacha Prime? <a href="{{route('auth.register')}}">Create an account</a></p>
</div></div></section>
@endsection