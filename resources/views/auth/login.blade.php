@extends('layouts.storefront')
@section('title', 'Login — Chacha Prime')
@section('content')
<div class="auth-shell"><div class="auth-card"><span class="eyebrow">WELCOME BACK</span><h1>Sign in</h1><p>Secure access with email verification and two-factor authentication.</p><form><label>Email<input type="email" name="email" required></label><label>Password<input type="password" name="password" required></label><button class="button button-dark" type="submit">Continue</button></form></div></div>
@endsection
