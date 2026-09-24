@extends('layouts.customer')
@section('title','Reviews & Q&A — Chacha Prime')
@section('content')
<h1 class="cp-customer-page-title">Reviews & Q&A</h1>
<section class="cp-customer-panel"><h2 style="margin:0 0 8px;font-size:17px">My product reviews</h2>
@forelse($reviews as $review)<article style="padding:15px 0;border-bottom:1px solid #eef2f7"><div style="display:flex;justify-content:space-between;gap:10px"><strong style="font-size:12px">{{$review->product_name}}</strong><span style="font-size:9px;font-weight:900;padding:4px 7px;border-radius:999px;background:#f1f5f9">{{ucfirst($review->status)}}</span></div><div style="font-size:10px;color:#334155;margin-top:5px">Rating: {{$review->rating}} / 5</div>@if($review->title)<div style="font-size:11px;font-weight:800;margin-top:7px">{{$review->title}}</div>@endif@if($review->body)<div style="font-size:11px;color:#64748b;line-height:1.6;margin-top:5px">{{$review->body}}</div>@endif</article>@empty<p class="cp-customer-muted">You have not submitted any product reviews yet.</p>@endforelse
{{$reviews->links()}}</section>
@endsection