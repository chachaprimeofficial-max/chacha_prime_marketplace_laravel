@extends('admin.layout')
@section('title','Support Case #'.$conversation->id.' — Chacha Prime')
@section('page_heading','Support Case #'.$conversation->id)
@section('content')
@if(session('success'))<div class="notice">{{session('success')}}</div>@endif
<div class="dashboard-two">
<section class="panel">
<div style="display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap;align-items:start">
<div><div style="font-size:10px;font-weight:900;letter-spacing:1px;color:#b45309">CASE #{{$conversation->id}}</div><h2 style="margin:5px 0">{{$conversation->subject}}</h2><p style="font-size:11px;color:#64748b;margin:0">{{$conversation->type}} · {{$conversation->department}} · {{$conversation->priority}}</p></div>
<a href="{{route('admin.support')}}" style="padding:9px 12px;border-radius:9px;background:#111827;color:#fff;text-decoration:none;font-size:11px;font-weight:800">← Queue</a>
</div>
<div id="thread" style="margin-top:16px;display:grid;gap:9px;max-height:560px;overflow:auto">
@foreach($messages as $m)
<div style="padding:11px 13px;border-radius:12px;background:{{in_array($m->sender_role,['customer','vendor'])?'#f8fafc':'#eff6ff'}};border:1px solid #e5e7eb">
<div style="font-size:9px;font-weight:900;text-transform:uppercase;color:#64748b">{{$m->sender_role}} · {{$m->sender_name ?: 'AI / System'}}</div>
<div style="font-size:12px;line-height:1.6;margin-top:4px;white-space:pre-wrap">{{ $m->body }}</div>
<div style="font-size:9px;color:#94a3b8;margin-top:5px">{{$m->created_at}}</div>
</div>
@endforeach
</div>
<form method="POST" action="{{route('admin.support.message',$conversation->id)}}" style="margin-top:12px">@csrf<textarea name="body" rows="4" required placeholder="Reply to customer or seller..."></textarea><button style="margin-top:8px;padding:10px 14px;border:0;border-radius:9px;background:#111827;color:#fff;font-weight:800">Send Reply</button></form>
</section>
<section style="display:grid;gap:14px;align-content:start">
<div class="panel"><h3>Case Controls</h3>
<form method="POST" action="{{route('admin.support.status',$conversation->id)}}" style="display:grid;gap:8px">@csrf
<select name="status">@foreach(['open','pending','ai_handled','closed'] as $x)<option value="{{$x}}" @selected($conversation->status===$x)>{{$x}}</option>@endforeach</select>
<select name="priority">@foreach(['low','normal','high','urgent'] as $x)<option value="{{$x}}" @selected($conversation->priority===$x)>{{$x}}</option>@endforeach</select>
<button style="padding:9px;border:0;border-radius:9px;background:#111827;color:#fff">Update Case</button></form></div>
<div class="panel"><h3>Assign Team Member</h3><form method="POST" action="{{route('admin.support.assign',$conversation->id)}}" style="display:grid;gap:8px">@csrf<select name="assigned_to"><option value="">Unassigned</option>@foreach($staff as $s)<option value="{{$s->id}}" @selected($conversation->assigned_to==$s->id)>{{$s->name}} · {{$s->role}}</option>@endforeach</select><button style="padding:9px;border:0;border-radius:9px;background:#111827;color:#fff">Save Assignment</button></form></div>
<div class="panel"><h3>Contact</h3><div style="font-size:11px;color:#475569">Customer: <b>{{$conversation->customer_name ?: '—'}}</b><br>Email: {{$conversation->customer_email ?: '—'}}<br>Seller: {{$conversation->vendor_name ?: $conversation->vendor_user_name ?: '—'}}<br>Order ID: {{$conversation->order_id ?: '—'}}</div></div>
</section>
</div>
@endsection
