@extends('admin.layout')
@section('title','Payments — Chacha Prime')
@section('page_heading','Payments Management')
@section('content')
<div class="panel"><h2>Payments</h2>@if(session('success'))<p style="color:#17652a">{{session('success')}}</p>@endif
<table style="width:100%;border-collapse:collapse"><tr><th align="left">Reference</th><th>User</th><th>Amount</th><th>Method</th><th>Status</th><th>Action</th></tr>
@foreach($payments as $p)<tr><td style="padding:12px 4px">{{ $p->transaction_reference ?: '—' }}</td><td>{{ $p->user->name ?? '-' }}</td><td>{{ $p->amount }} {{ $p->currency }}</td><td>{{ $p->method_id }}</td><td>{{ $p->status }}</td><td><form method="POST" action="{{route('admin.payments.update',$p)}}">@csrf<select name="status"><option>pending</option><option>paid</option><option>failed</option><option>refunded</option></select><button class="button button-dark">Save</button></form></td></tr>@endforeach</table>{{ $payments->links() }}</div>
@endsection