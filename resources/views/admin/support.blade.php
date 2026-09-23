@extends('admin.layout')
@section('title','Support Center — Chacha Prime')
@section('page_heading','Support Center')
@section('content')
@if(session('success'))<div class="notice">{{session('success')}}</div>@endif
<div class="admin-grid">
@foreach([['Open Cases',$stats['open']],['AI Handled',$stats['ai']],['Customer Cases',$stats['customer']],['Seller Cases',$stats['vendor']]] as $s)
<div class="admin-stat"><span class="label">{{$s[0]}}</span><strong>{{$s[1]}}</strong><small>Live support queue</small></div>
@endforeach
</div>
<div class="dashboard-two" style="margin-top:14px">
<section class="panel">
<div class="section-title" style="margin-top:0"><div><h2>Conversation Queue</h2><p>AI escalations, customer support and seller support in one workspace.</p></div></div>
<form method="GET" style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:14px">
<select name="status"><option value="">All statuses</option>@foreach(['open','pending','ai_handled','closed'] as $x)<option value="{{$x}}" @selected($status===$x)>{{$x}}</option>@endforeach</select>
<select name="type"><option value="">All types</option>@foreach(['customer_support','vendor_support','customer_vendor'] as $x)<option value="{{$x}}" @selected($type===$x)>{{$x}}</option>@endforeach</select>
<select name="department"><option value="">All teams</option>@foreach(['customer_support','orders','payments','returns','technical','vendor_support','seller_support'] as $x)<option value="{{$x}}" @selected($department===$x)>{{$x}}</option>@endforeach</select>
<button style="padding:9px 12px;border:0;border-radius:9px;background:#111827;color:#fff">Filter</button>
</form>
<div class="table-wrap"><table><thead><tr><th>Case</th><th>Customer / Seller</th><th>Team</th><th>Status</th><th>Priority</th><th>Assigned</th></tr></thead><tbody>
@forelse($conversations as $c)
<tr><td><a href="{{route('admin.support.thread',$c->id)}}" style="font-weight:800;color:#b45309;text-decoration:none">#{{$c->id}} · {{Str::limit($c->subject,34)}}</a><div style="color:#94a3b8;font-size:9px">{{optional($c->last_message_at)->format('Y-m-d H:i') ?? $c->last_message_at}}</div></td>
<td>{{ $c->customer_name ?: ($c->vendor_name ?: $c->vendor_user_name ?: '—') }}<div style="font-size:9px;color:#94a3b8">{{$c->type}}</div></td>
<td>{{$c->department}}</td><td><span class="status">{{$c->status}}</span></td><td>{{$c->priority}}</td><td>{{$c->assigned_name ?: 'Unassigned'}}</td></tr>
@empty<tr><td colspan="6">No support conversations yet.</td></tr>@endforelse
</tbody></table></div>{{$conversations->links()}}
</section>
<section class="panel">
<h2>Customer Contact Channels</h2><p style="font-size:11px;color:#64748b">These appear in the AI support page and customer support center. Keep only official business contact details here.</p>
<form method="POST" action="{{route('admin.support.settings')}}" style="display:grid;gap:9px">@csrf
<label style="font-size:10px;font-weight:800">WhatsApp number</label><input name="whatsapp" value="{{$channels['whatsapp']}}" placeholder="+923001234567">
<label style="font-size:10px;font-weight:800">WeChat ID</label><input name="wechat" value="{{$channels['wechat']}}" placeholder="ChachaPrimeSupport">
<label style="font-size:10px;font-weight:800">Support email</label><input name="email" type="email" value="{{$channels['email']}}" required>
<button style="padding:10px;border:0;border-radius:9px;background:#111827;color:#fff">Save Contact Options</button>
</form>
<div style="margin-top:16px;padding:12px;background:#f8fafc;border-radius:10px;font-size:10px;color:#475569">AI policy: the assistant can read the signed-in customer's marketplace context and support history, but financial/account-changing actions remain protected behind the human team.</div>
</section>
</div>
@endsection
