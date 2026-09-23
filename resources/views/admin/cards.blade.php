@extends('admin.layout')
@section('title','Virtual Cards — Chacha Prime')
@section('page_heading','Marketplace Virtual Cards')
@section('content')
<div class="panel"><h2>Internal Marketplace Cards</h2><p>These are closed-loop Chacha Prime marketplace balances, not bank-issued Visa/Mastercard cards.</p>@if(session('success'))<p style="color:#17652a">{{session('success')}}</p>@endif
<form method="POST" action="{{route('admin.cards.issue')}}" style="display:flex;gap:8px;margin-bottom:20px">@csrf<input name="user_id" placeholder="Customer User ID" required><button class="button button-dark">Issue Card</button></form>
<table style="width:100%;border-collapse:collapse"><tr><th align="left">Customer</th><th>Card</th><th>Expiry</th><th>Status</th></tr>@foreach($cards as $c)<tr><td style="padding:12px 4px">{{ $c->user->name ?? '-' }}</td><td>•••• •••• •••• {{ $c->last4 }}</td><td>{{ $c->expiry_month }}/{{ $c->expiry_year }}</td><td>{{ $c->status }}</td></tr>@endforeach</table>{{ $cards->links() }}</div>
@endsection