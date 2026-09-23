@extends('admin.layout')
@section('title','Orders — Chacha Prime')
@section('page_heading','Orders Management')
@section('content')
<div class="panel"><h2>Orders</h2>@if(session('success'))<p style="color:#17652a">{{session('success')}}</p>@endif
<table style="width:100%;border-collapse:collapse"><tr><th align="left">Order</th><th>Customer</th><th>Total</th><th>Status</th><th>Payment</th><th>Action</th></tr>
@foreach($orders as $o)<tr><td style="padding:12px 4px">{{ $o->order_number }}</td><td>{{ $o->user->name ?? '-' }}</td><td>{{ $o->grand_total }} {{ $o->currency }}</td><td>{{ $o->status }}</td><td>{{ $o->payment_status }}</td><td><form method="POST" action="{{route('admin.orders.update',$o)}}" style="display:flex;gap:5px">@csrf<select name="status"><option>pending</option><option>processing</option><option>completed</option><option>cancelled</option></select><select name="payment_status"><option>unpaid</option><option>pending</option><option>paid</option><option>refunded</option></select><select name="fulfillment_status"><option>unfulfilled</option><option>processing</option><option>shipped</option><option>delivered</option></select><button class="button button-dark">Save</button></form></td></tr>@endforeach</table>{{ $orders->links() }}</div>
@endsection