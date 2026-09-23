@extends('layouts.storefront')
@section('title','Two-Factor Authentication — Chacha Prime')
@push('styles')
<style>
.cp-totp{min-height:calc(100vh - 104px);background:#f5f7fb;padding:34px 16px}.cp-totp-card{width:min(540px,100%);margin:18px auto;background:#fff;border:1px solid #e5e7eb;border-radius:22px;padding:34px;box-shadow:0 24px 70px #0f172a14;text-align:center}.cp-totp-brand{height:48px;display:flex;justify-content:center}.cp-totp-brand img{height:48px;width:190px;object-fit:contain}.cp-totp-line{height:1px;background:#e5e7eb;margin:16px 0 24px}.cp-totp-icon{width:54px;height:54px;border-radius:16px;background:#eff6ff;color:#2563eb;display:grid;place-items:center;margin:0 auto 15px;font-size:24px}.cp-totp-eyebrow{font-size:10px;font-weight:900;letter-spacing:1.8px;color:#2563eb}.cp-totp-card h1{margin:7px 0;font-size:27px;color:#0f172a}.cp-totp-card p{color:#64748b;font-size:12px;line-height:1.6}.cp-totp-setup{margin:17px auto;padding:16px;border:1px solid #dbeafe;background:#eff6ff;border-radius:15px;max-width:330px}.cp-qr{width:220px;height:220px;margin:0 auto 12px;background:#fff;border:1px solid #dbe1e8;border-radius:12px;padding:8px;display:grid;place-items:center}.cp-qr img,.cp-qr canvas{max-width:100%;max-height:100%}.cp-secret{font-size:11px;color:#1e3a8a;word-break:break-all;background:#fff;border:1px solid #bfdbfe;border-radius:8px;padding:9px}.cp-totp-error{padding:11px 13px;border-radius:10px;background:#fef3f2;color:#b42318;border:1px solid #fecdca;font-size:11px;margin:14px 0;text-align:left}.cp-totp-form{display:grid;gap:12px;margin-top:18px}.cp-totp-form input{width:100%;padding:13px;border:1px solid #dbe1e8;border-radius:11px;text-align:center;letter-spacing:8px;font-size:22px;font-weight:900;outline:none}.cp-totp-form input:focus{border-color:#2563eb;box-shadow:0 0 0 3px #2563eb14}.cp-totp-btn{border:0;border-radius:11px;padding:13px;background:#0f172a;color:#fff;font-weight:900;cursor:pointer}.cp-totp-foot{margin-top:16px;font-size:11px}.cp-totp-foot a{color:#2563eb;font-weight:800;text-decoration:none}
</style>
@endpush
@section('content')
<section class="cp-totp"><div class="cp-totp-card"><div class="cp-totp-brand"><img src="{{asset('images/chacha-logo.svg')}}" alt="CHACHA 查查 Prime"></div><div class="cp-totp-line"></div><div class="cp-totp-icon">🔐</div><span class="cp-totp-eyebrow">SECURITY STEP 2</span><h1>Google Authenticator</h1><p>Scan the QR code with Google Authenticator, Microsoft Authenticator, Authy or another compatible TOTP app.</p>
@if($setup)<div class="cp-totp-setup"><div id="cpTotpQr" class="cp-qr" data-uri="{{e($setup['uri'])}}"></div><div class="cp-secret"><b>Manual setup key</b><br>{{ $setup['secret'] }}</div></div>@endif
@if($errors->any())<div class="cp-totp-error">@foreach($errors->all() as $error)<div>{{$error}}</div>@endforeach</div>@endif
<form class="cp-totp-form" method="POST" action="{{route('auth.totp.verify')}}">@csrf<input name="code" inputmode="numeric" maxlength="6" pattern="[0-9]{6}" autocomplete="one-time-code" required autofocus placeholder="••••••"><button class="cp-totp-btn" type="submit">Verify & Continue →</button></form>
<div class="cp-totp-foot"><a href="{{route('auth.login')}}">← Back to login</a></div></div></section>
@endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
(function(){const el=document.getElementById('cpTotpQr');if(!el)return;const uri=el.dataset.uri;if(window.QRCode&&uri){new QRCode(el,{text:uri,width:200,height:200,colorDark:'#0f172a',colorLight:'#ffffff',correctLevel:QRCode.CorrectLevel.M});}})();
</script>
@endpush