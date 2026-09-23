@extends('layouts.storefront')
@section('title','Vendor Dashboard')
@section('content')
<div class="container"><div class="dash-head"><div><span class="eyebrow">SELLER CENTER</span><h1>{{ $vendor->store_name ?: 'My Store' }}</h1><p>Manage your Chacha Prime store from one place.</p></div><a class="button" href="{{route('vendor.products')}}">Manage products</a></div>
<div class="stats">@foreach($stats as $k=>$v)<div class="stat"><small>{{ucwords(str_replace('_',' ',$k))}}</small><strong>{{is_numeric($v)?(in_array($k,['sales'])?number_format($v,2):number_format($v)):$v}}</strong></div>@endforeach</div>
<div class="quick"><a href="{{route('vendor.products')}}">Products</a><a href="{{route('vendor.orders')}}">Orders</a><a href="{{route('vendor.wallet')}}">Wallet</a><a href="{{route('vendor.reviews')}}">Reviews</a></div>
<div class="panel"><h2>Recent Products</h2><div class="table-wrap"><table><tr><th>Name</th><th>Price</th><th>Stock</th><th>Status</th></tr>@foreach($recentProducts as $p)<tr><td>{{$p->name}}</td><td>{{$p->price}}</td><td>{{$p->stock_quantity}}</td><td>{{$p->status}}</td></tr>@endforeach</table></div></div></div>
@endsection