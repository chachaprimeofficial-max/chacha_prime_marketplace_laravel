@extends('admin.layout')
@section('title','Wallets — Chacha Prime')
@section('page_heading','Wallet Management')
@section('content')
<div class="panel"><h2>Wallets</h2>@if(session('success'))<p style="color:#17652a">{{session('success')}}</p>@endif
<table style="width:100%;border-collapse:collapse"><tr><th align="left">User</th><th>Currency</th><th>Balance</th><th>Adjustment</th></tr>
@foreach($wallets as $w)<tr><td style="padding:12px 4px">{{ $w->user->name ?? '-' }}</td><td>{{ $w->currency }}</td><td>{{ $w->balance }}</td><td><form method="POST" action="{{route('admin.wallets.adjust',$w)}}" style="display:flex;gap:6px">@csrf<input name="amount" placeholder="+/- amount" required><input name="reason" placeholder="Reason" required><button class="button button-dark">Adjust</button></form></td></tr>@endforeach</table>{{ $wallets->links() }}</div>
@endsection