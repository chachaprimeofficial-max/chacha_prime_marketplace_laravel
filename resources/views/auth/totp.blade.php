@extends('layouts.storefront')
@section('title','Two-Factor Authentication — Chacha Prime')
@section('content')
<section class="auth-shell"><div class="auth-card"><span class="eyebrow">SECURITY STEP 2</span><h1>Authenticator code</h1>
@if($setup)<div class="notice success"><strong>First-time setup</strong><p>Add this account to Google Authenticator or another TOTP app.</p><p><b>Setup key:</b><br><code style="word-break:break-all">{{ $setup['secret'] }}</code></p><p style="font-size:12px;word-break:break-all">Manual URI: {{ $setup['uri'] }}</p></div>@endif
<p>Open your authenticator app and enter the current 6-digit code.</p>
@if($errors->any())<div class="notice error">@foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach</div>@endif
<form method="POST" action="{{ route('auth.totp.verify') }}">@csrf<label>Authenticator code<input name="code" inputmode="numeric" maxlength="6" pattern="[0-9]{6}" required autofocus></label><button class="button button-dark" type="submit">Complete sign in</button></form>
</div></section>
@endsection