@extends('layouts.storefront')
@section('title','Verify Email — Chacha Prime')
@section('content')
<section class="auth-shell"><div class="auth-card"><span class="eyebrow">SECURITY STEP 1</span><h1>Verify your email</h1><p>Enter the 6-digit code sent to your email address.</p>
@if($errors->any())<div class="notice error">@foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach</div>@endif
@if(session('success'))<div class="notice success">{{ session('success') }}</div>@endif
<form method="POST" action="{{ route('auth.otp.verify') }}">@csrf<label>6-digit OTP<input name="code" inputmode="numeric" maxlength="6" pattern="[0-9]{6}" required autofocus></label><button class="button button-dark" type="submit">Verify OTP</button></form>
</div></section>
@endsection