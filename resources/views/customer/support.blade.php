@extends('layouts.customer')
@section('title','Customer Support — Chacha Prime')
@push('styles')<style>.cp-support{background:#f5f7fa;min-height:calc(100vh - 104px);padding:22px}.cp-support-shell{max-width:1450px;margin:auto}.cp-support-hero,.cp-support-card{background:#fff;border:1px solid #e2e8f0;border-radius:18px;box-shadow:0 8px 25px #11182708}.cp-support-hero{padding:22px;margin-bottom:15px}.cp-support-grid{display:grid;grid-template-columns:1.25fr .75fr;gap:15px}.cp-support-card{padding:20px}.cp-support h1{font-size:29px;margin:6px 0}.cp-support p{color:#64748b;font-size:12px}.cp-support a{display:inline-flex;padding:10px 13px;border-radius:10px;text-decoration:none;font-size:11px;font-weight:800;background:#111827;color:#fff}@media(max-width:800px){.cp-support{padding:12px}.cp-support-grid{grid-template-columns:1fr}}</style>@endpush
@section('content')
<div class="cp-support"><div class="cp-support-shell"><div class="cp-support-hero"><div style="font-size:10px;font-weight:900;letter-spacing:2px;color:#d97706">CHACHA PRIME SUPPORT</div><h1>Help, AI chat & human support</h1><p>Get account-aware assistance, order help and human escalation from one Customer Center workspace.</p></div><div class="cp-support-grid">
@if(session('success'))<div style="padding:12px;background:#ecfdf3;border:1px solid #abefc6;border-radius:12px;margin-bottom:14px;font-size:12px">{{session('success')}}</div>@endif
<div style="display:grid;grid-template-columns:1.2fr .8fr;gap:16px">
<section class="cp-support-card">
<div style="font-size:10px;font-weight:900;letter-spacing:2px;color:#d97706">CHACHA PRIME SUPPORT</div>
<h1 style="margin:7px 0;font-size:26px">Help, AI chat & human support</h1>
<p style="color:#64748b;font-size:12px">Start with the AI assistant. If the issue needs a person, your case is transferred with the conversation and verified account/order context.</p>
<div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:12px">
<a href="{{route('ai.assistant')}}" style="padding:10px 13px;background:#111827;color:#fff;border-radius:10px;text-decoration:none;font-size:11px;font-weight:800">Open AI Support</a>
@if($channels['whatsapp'])<a target="_blank" href="https://wa.me/{{preg_replace('/\D+/','',$channels['whatsapp'])}}" style="padding:10px 13px;background:#ecfdf3;color:#166534;border-radius:10px;text-decoration:none;font-size:11px;font-weight:800">WhatsApp</a>@endif
@if($channels['wechat'])<button type="button" onclick="navigator.clipboard?.writeText(@js($channels['wechat']));alert('WeChat ID copied.')" style="padding:10px 13px;border:0;background:#f0fdf4;color:#166534;border-radius:10px;font-size:11px;font-weight:800">WeChat: {{$channels['wechat']}}</button>@endif
<a href="mailto:{{$channels['email']}}" style="padding:10px 13px;background:#eff6ff;color:#1d4ed8;border-radius:10px;text-decoration:none;font-size:11px;font-weight:800">Email Support</a>
</div>
</section>
<section style="background:#0b1220;color:#fff;border-radius:20px;padding:20px">
<div style="font-size:10px;letter-spacing:1.5px;color:#fbbf24;font-weight:900">OPEN A HUMAN CASE</div>
<form method="POST" action="{{route('customer.support.start')}}" style="display:grid;gap:8px;margin-top:10px">@csrf
<input name="subject" placeholder="Subject (e.g. payment issue)" maxlength="190">
<textarea name="message" rows="4" required placeholder="Explain the issue..."></textarea>
<button style="padding:10px;border:0;border-radius:9px;background:#f59e0b;color:#111827;font-weight:900">Contact Support Team</button>
</form>
</section>
</div>
<div style="display:grid;grid-template-columns:.75fr 1.25fr;gap:16px;margin-top:16px">
<section style="background:#fff;border:1px solid #e5e7eb;border-radius:20px;padding:18px"><h3 style="margin-top:0">My Support Cases</h3>
<div style="display:grid;gap:7px">@forelse($conversations as $c)<a href="{{route('customer.support',['conversation'=>$c->id])}}" style="text-decoration:none;color:#111827;padding:11px;border:1px solid #e5e7eb;border-radius:11px"><b>#{{$c->id}} · {{Str::limit($c->subject,32)}}</b><div style="font-size:9px;color:#64748b;margin-top:4px">{{$c->type}} · {{$c->status}} · {{$c->department}}</div></a>@empty<p style="font-size:11px;color:#94a3b8">No cases yet.</p>@endforelse</div>
</section>
<section style="background:#fff;border:1px solid #e5e7eb;border-radius:20px;padding:18px"><h3 style="margin-top:0">Conversation</h3>
@if($conversation)
<div style="max-height:430px;overflow:auto;display:grid;gap:8px" id="customerThread">@foreach($messages as $m)<div style="padding:10px;border-radius:11px;background:{{in_array($m->sender_role,['customer','ai'])?'#f8fafc':'#eff6ff'}}"><div style="font-size:9px;font-weight:900;color:#64748b">{{$m->sender_role}}</div><div style="font-size:11px;white-space:pre-wrap;line-height:1.55">{{$m->body}}</div></div>@endforeach</div>
<form method="POST" action="{{route('customer.support.message',$conversation->id)}}" style="margin-top:10px">@csrf<textarea name="body" rows="3" required placeholder="Reply..."></textarea><button style="margin-top:7px;padding:9px 12px;border:0;border-radius:9px;background:#111827;color:#fff">Send</button></form>
@else<p style="font-size:11px;color:#94a3b8">Open a case or start from the AI assistant.</p>@endif
</section>
</div>
</div></div></div>
@endsection
