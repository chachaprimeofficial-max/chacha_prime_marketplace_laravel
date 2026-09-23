@extends('vendor.layout')
@section('title','Vendor Wallet')
@section('content')<div class="container"><h1>Wallet</h1><div class="panel"><div class="stat"><small>Available Balance</small><strong>{{number_format($wallet->balance ?? 0,2)}}</strong></div><p>Payouts and wallet transactions are controlled by the marketplace.</p></div></div>@endsection