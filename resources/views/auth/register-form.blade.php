@extends('layouts.storefront')
@section('title','Create Account — Chacha Prime')
@push('styles')
<style>
.reg-page{min-height:calc(100vh - 104px);background:#f5f7fb;padding:32px 16px}.reg-wrap{width:min(1050px,100%);margin:auto;background:#fff;border:1px solid #e5e7eb;border-radius:24px;box-shadow:0 20px 60px #0f172a14;overflow:hidden}.reg-head{background:#0b1220;color:#fff;padding:28px 34px;display:flex;align-items:center;gap:20px}.reg-head img{width:190px;height:52px;object-fit:contain}.reg-head h1{margin:0;font-size:25px}.reg-head p{margin:5px 0 0;color:#cbd5e1;font-size:11px}.reg-body{padding:30px 34px}.ey{font-size:10px;font-weight:900;letter-spacing:1.8px;color:#d97706}.intro{color:#64748b;font-size:12px}.roles{display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin:18px 0}.role{border:1px solid #e5e7eb;border-radius:13px;padding:13px;cursor:pointer}.role.active{border-color:#f59e0b;background:#fffbeb;box-shadow:0 0 0 2px #f59e0b1a}.role b{font-size:12px}.role span{display:block;color:#64748b;font-size:10px;margin-top:4px}.role input{display:none}.form{display:grid;grid-template-columns:1fr 1fr;gap:12px}.field{display:grid;gap:6px;font-size:11px;font-weight:800;color:#334155}.field.full{grid-column:1/-1}.field input{padding:12px;border:1px solid #dbe1e8;border-radius:10px;font-size:12px}.field input:focus{border-color:#f59e0b;outline:0;box-shadow:0 0 0 3px #f59e0b1a}.submit{grid-column:1/-1;border:0;background:#111827;color:#fff;border-radius:11px;padding:13px;font-weight:900;cursor:pointer}.notice{padding:11px;border-radius:10px;font-size:11px;background:#fef3f2;color:#b42318;border:1px solid #fecdca;margin:12px 0}.foot{text-align:center;color:#64748b;font-size:12px;margin-top:18px}.foot a{color:#b45309;font-weight:900;text-decoration:none}@media(max-width:700px){.reg-head{padding:22px;flex-direction:column;align-items:flex-start}.reg-body{padding:22px}.roles,.form{grid-template-columns:1fr}.field.full,.submit{grid-column:1}}
</style>
@endpush
@section('content')
<section class="reg-page"><div class="reg-wrap">
<header class="reg-head"><img src="{{asset('images/chacha-logo.svg')}}" alt="CHACHA 查查 Prime"><div><h1>Create your marketplace account</h1><p>Choose your account type, then complete email and authenticator verification.</p></div></header>
<div class="reg-body"><span class="ey">JOIN CHACHA PRIME</span><p class="intro">Select the marketplace role you want to register for.</p>
@if($errors->any())<div class="notice">@foreach($errors->all() as $error)<div>{{$error}}</div>@endforeach</div>@endif
<form method="POST" action="{{route('auth.register.submit')}}" class="form">@csrf
<div class="field full"><span>Account type</span><div class="roles">
<label class="role {{old('type',$type)==='customer'?'active':''}}"><input type="radio" name="type" value="customer" @checked(old('type',$type)==='customer')><b>B2C Customer</b><span>Retail shopping account</span></label>
<label class="role {{old('type',$type)==='b2b_customer'?'active':''}}"><input type="radio" name="type" value="b2b_customer" @checked(old('type',$type)==='b2b_customer')><b>B2B Customer</b><span>Business purchasing account</span></label>
<label class="role {{old('type',$type)==='vendor'?'active':''}}"><input type="radio" name="type" value="vendor" @checked(old('type',$type)==='vendor')><b>Vendor / Seller</b><span>Sell products on Chacha Prime</span></label>
</div></div>
<label class="field">Full name<input name="name" value="{{old('name')}}" autocomplete="name" required></label>
<label class="field">Phone<input name="phone" value="{{old('phone')}}" autocomplete="tel"></label>
<label class="field full">Email<input type="email" name="email" value="{{old('email')}}" autocomplete="email" required></label>
<label class="field">Password<input type="password" name="password" autocomplete="new-password" minlength="8" required></label>
<label class="field">Confirm password<input type="password" name="password_confirmation" autocomplete="new-password" minlength="8" required></label>
<button class="submit" type="submit">Create account & continue →</button></form>
<div class="foot">Already registered? <a href="{{route('auth.login')}}">Sign in</a></div></div></div></section>
@endsection