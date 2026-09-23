@extends('layouts.storefront')
@section('title','AI & Customer Support — Chacha Prime')
@section('content')
<div style="max-width:1120px;margin:0 auto;padding:28px 16px 60px">
<div style="display:grid;grid-template-columns:1.35fr .65fr;gap:16px">
<section style="background:#fff;border:1px solid #e5e7eb;border-radius:22px;box-shadow:0 14px 45px #0f172a0a;overflow:hidden">
<div style="padding:20px 22px;background:#0b1220;color:#fff"><div style="font-size:10px;letter-spacing:2px;color:#fbbf24;font-weight:900">CHACHA PRIME AI SUPPORT</div><h1 style="margin:6px 0;font-size:25px">How can we help?</h1><p style="margin:0;color:#94a3b8;font-size:11px">The AI can use your signed-in account, orders, returns, cart and marketplace data. If a human needs to act, the complete case is transferred to the support team.</p></div>
@auth
<div id="chat" style="min-height:390px;max-height:560px;overflow:auto;padding:18px;display:grid;gap:10px">
@foreach($messages as $m)<div style="max-width:86%;padding:11px 13px;border-radius:14px;background:{{in_array($m->sender_role,['customer'])?'#111827':'#f8fafc'}};color:{{in_array($m->sender_role,['customer'])?'#fff':'#111827'}};justify-self:{{in_array($m->sender_role,['customer'])?'end':'start'}}"><div style="font-size:9px;font-weight:900;text-transform:uppercase;opacity:.65">{{$m->sender_role}}</div><div style="font-size:12px;line-height:1.6;white-space:pre-wrap;margin-top:3px">{{$m->body}}</div></div>@endforeach
</div>
<form id="ai-form" style="display:flex;gap:8px;padding:14px;border-top:1px solid #e5e7eb">@csrf<input id="ai-message" name="message" maxlength="2000" required placeholder="Ask about an order, product, return, payment or any issue..." style="flex:1;padding:12px;border:1px solid #dbe1e8;border-radius:11px"><button class="button button-dark" style="border:0;border-radius:11px;padding:0 18px">Send</button></form>
<div id="ai-status" style="padding:0 15px 12px;color:#64748b;font-size:10px"></div>
@else
<div style="padding:35px;text-align:center"><h3>Sign in to use account-aware AI support</h3><p style="font-size:11px;color:#64748b">This lets Chacha Prime securely check your orders and support history.</p><a href="{{route('auth.login')}}" style="display:inline-block;padding:10px 15px;background:#111827;color:#fff;border-radius:10px;text-decoration:none;font-size:11px;font-weight:800">Sign In</a></div>
@endauth
</section>
<section style="display:grid;gap:12px;align-content:start">
<div style="background:#fff;border:1px solid #e5e7eb;border-radius:18px;padding:18px"><div style="font-size:10px;letter-spacing:1.4px;color:#d97706;font-weight:900">HUMAN TEAM</div><h3 style="margin:6px 0">Need a person?</h3><p style="font-size:11px;color:#64748b">Ask the AI to transfer you, or open a human support case directly.</p><a href="{{route('customer.support')}}" style="display:block;padding:10px;border-radius:10px;background:#111827;color:#fff;text-align:center;text-decoration:none;font-size:11px;font-weight:800">Open Support Center</a></div>
<div style="background:#fff;border:1px solid #e5e7eb;border-radius:18px;padding:18px"><h3 style="margin-top:0">Direct Contact</h3><div style="display:grid;gap:8px">
@if($channels['whatsapp'])<a target="_blank" href="https://wa.me/{{preg_replace('/\D+/','',$channels['whatsapp'])}}" style="padding:10px;border-radius:10px;background:#ecfdf3;color:#166534;text-decoration:none;font-size:11px;font-weight:800">WhatsApp Support</a>@endif
@if($channels['wechat'])<button type="button" onclick="navigator.clipboard?.writeText(@js($channels['wechat']));alert('WeChat ID copied.')" style="padding:10px;border:0;border-radius:10px;background:#f0fdf4;color:#166534;font-size:11px;font-weight:800">WeChat: {{$channels['wechat']}}</button>@endif
<a href="mailto:{{$channels['email']}}" style="padding:10px;border-radius:10px;background:#eff6ff;color:#1d4ed8;text-decoration:none;font-size:11px;font-weight:800">Email: {{$channels['email']}}</a>
</div></div>
<div style="background:#f8fafc;border:1px solid #e5e7eb;border-radius:18px;padding:16px;font-size:10px;color:#64748b"><b style="color:#111827">AI safety</b><br>AI answers are grounded in marketplace data. Account-changing, payment and refund decisions remain protected behind the support team.</div>
</section>
</div></div>
@auth
<script>
const form=document.getElementById('ai-form'),input=document.getElementById('ai-message'),chat=document.getElementById('chat'),status=document.getElementById('ai-status');
let conversationId=@json($conversation?->id);
function add(role,text){const wrap=document.createElement('div');wrap.style.cssText='max-width:86%;padding:11px 13px;border-radius:14px;white-space:normal;line-height:1.6;font-size:12px;'+(role==='customer'?'background:#111827;color:#fff;justify-self:end':'background:#f8fafc;color:#111827;justify-self:start');const label=document.createElement('div');label.style.cssText='font-size:9px;font-weight:900;text-transform:uppercase;opacity:.65';label.textContent=role;const body=document.createElement('div');body.style.marginTop='3px';body.textContent=text;wrap.append(label,body);chat.appendChild(wrap);chat.scrollTop=chat.scrollHeight}
form?.addEventListener('submit',async e=>{e.preventDefault();const message=input.value.trim();if(!message)return;add('customer',message);input.value='';const btn=form.querySelector('button');btn.disabled=true;status.textContent='AI is checking your marketplace data...';
try{const res=await fetch('{{route('ai.assistant.ask')}}',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{csrf_token()}}','Accept':'application/json'},body:JSON.stringify({message,conversation_id:conversationId})});const data=await res.json();if(data.conversation_id)conversationId=data.conversation_id;add('ai',data.answer||data.message||'Please contact support.');status.textContent=data.escalate?'Your case has been transferred to the human support queue.':'AI support handled this request.';if(data.escalate){setTimeout(()=>{status.innerHTML='<a href="{{route('customer.support')}}" style="color:#b45309;font-weight:800">Open your human support case →</a>'},700)}}catch(err){add('system','AI service is temporarily unavailable. Please open a human support case.');status.innerHTML='<a href="{{route('customer.support')}}" style="color:#b45309;font-weight:800">Open Support Center →</a>'}finally{btn.disabled=false;chat.scrollTop=chat.scrollHeight;}});
chat.scrollTop=chat.scrollHeight;
</script>
@endauth
@endsection
