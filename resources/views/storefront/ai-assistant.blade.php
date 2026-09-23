@extends('layouts.storefront')
@section('title','AI Shopping Assistant')
@section('content')
<div class="dashboard-shell"><div class="panel">
<div class="eyebrow">CHACHA PRIME AI</div><h1>AI Shopping Assistant</h1>
<p>Ask about products available in our marketplace.</p>
<div id="chat" class="panel" style="min-height:260px;max-height:500px;overflow:auto"></div>
<form id="ai-form" style="display:flex;gap:10px;margin-top:16px">
@csrf<input id="ai-message" name="message" maxlength="1000" required placeholder="What product are you looking for?" style="flex:1">
<button class="button button-dark">Ask AI</button>
</form></div></div>
<script>
const form=document.getElementById('ai-form'), input=document.getElementById('ai-message'), chat=document.getElementById('chat');
form.addEventListener('submit',async e=>{e.preventDefault();const message=input.value.trim();if(!message)return;
chat.insertAdjacentHTML('beforeend','<p><strong>You:</strong> '+message.replace(/</g,'&lt;')+'</p>');input.value='';const btn=form.querySelector('button');btn.disabled=true;
try{const res=await fetch('{{route('ai.assistant.ask')}}',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{csrf_token()}}','Accept':'application/json'},body:JSON.stringify({message})});const data=await res.json();chat.insertAdjacentHTML('beforeend','<p><strong>AI:</strong> '+String(data.answer||'No response').replace(/</g,'&lt;').replace(/\n/g,'<br>')+'</p>');}catch(err){chat.insertAdjacentHTML('beforeend','<p>AI service is temporarily unavailable.</p>')}finally{btn.disabled=false;chat.scrollTop=chat.scrollHeight;}});
</script>
@endsection
