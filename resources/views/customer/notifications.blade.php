@extends('layouts.customer')
@section('title','Notifications — Chacha Prime')
@push('styles')<style>.cp-notice{display:flex;justify-content:space-between;gap:15px;padding:14px 0;border-bottom:1px solid #eef2f7}.cp-notice:last-child{border-bottom:0}.cp-notice-title{font-size:12px;font-weight:900;color:#0f172a}.cp-notice-msg{font-size:10px;color:#64748b;margin-top:4px;line-height:1.5}.cp-notice-time{font-size:9px;color:#94a3b8;white-space:nowrap}</style>@endpush
@section('content')
<h1 class="cp-customer-page-title">Notifications</h1>
<section class="cp-customer-panel"><div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:7px"><h2 style="margin:0;font-size:17px">Account activity</h2><span class="cp-customer-muted">{{method_exists($notifications,'total')?$notifications->total():$notifications->count()}} notifications</span></div>
@forelse($notifications as $n)<article class="cp-notice"><div><div class="cp-notice-title">{{$n->title}}</div><div class="cp-notice-msg">{{$n->message}}</div></div><time class="cp-notice-time">{{$n->created_at}}</time></article>@empty<p class="cp-customer-muted">No notifications yet.</p>@endforelse
@if(method_exists($notifications,'links'))<div style="margin-top:14px">{{$notifications->links()}}</div>@endif
</section>
@endsection