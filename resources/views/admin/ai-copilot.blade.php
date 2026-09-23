@extends('admin.layout')
@section('title','AI Copilot — Chacha Prime')
@section('page_heading','AI Copilot')
@section('content')
<div class="panel" style="max-width:1100px">
<div style="font-size:10px;letter-spacing:2px;color:#d97706;font-weight:900">MARKETPLACE INTELLIGENCE</div><h1 style="margin:6px 0">Admin AI Copilot</h1><p style="font-size:11px;color:#64748b">Ask about products, vendors, orders, payments, support queue and marketplace operations. This assistant is read-only and does not receive credentials or secrets.</p>
<div id="adminChat" style="min-height:360px;max-height:540px;overflow:auto;border:1px solid #e5e7eb;border-radius:14px;padding:14px;display:grid;gap:8px;background:#f8fafc"></div>
<form id="adminAiForm" style="display:flex;gap:8px;margin-top:10px"><input id="adminAiInput" required maxlength="2000" placeholder="Example: How many pending orders and open support cases are there?" style="flex:1"><button style="padding:10px 14px;border:0;border-radius:9px;background:#111827;color:#fff;font-weight:800">Ask Copilot</button></form>
<div id="adminAiStatus" style="font-size:10px;color:#64748b;margin-top:7px"></div>
</div>
<script>
const f=document.getElementById('adminAiForm'),i=document.getElementById('adminAiInput'),c=document.getElementById('adminChat'),s=document.getElementById('adminAiStatus');
function add(role,body){const x=document.createElement('div');x.style.cssText='padding:10px;border-radius:10px;white-space:pre-wrap;font-size:11px;line-height:1.6;max-width:88%;justify-self:'+(role==='You'?'end':'start')+';background:'+(role==='You'?'#111827':'#fff')+';color:'+(role==='You'?'#fff':'#111827');x.innerHTML='<b style="font-size:9px;text-transform:uppercase;opacity:.65">'+role+'</b><div style="margin-top:3px"></div>';x.lastChild.textContent=body;c.appendChild(x);c.scrollTop=c.scrollHeight}
f.addEventListener('submit',async e=>{e.preventDefault();const q=i.value.trim();if(!q)return;add('You',q);i.value='';s.textContent='Checking live marketplace data...';f.querySelector('button').disabled=true;try{const r=await fetch('{{route('admin.ai-copilot.ask')}}',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{csrf_token()}}','Accept':'application/json'},body:JSON.stringify({message:q})});const j=await r.json();add('AI',j.answer||j.message||'No response.')}catch(e){add('AI','AI service is temporarily unavailable.')}finally{f.querySelector('button').disabled=false;s.textContent=''}})
</script>
@endsection
