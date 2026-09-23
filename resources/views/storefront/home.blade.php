@extends('layouts.storefront')
@section('title','Chacha Prime — Premium Marketplace')
@section('content')
@include('partials.hero-carousel')
<section class="cp-home-content"><div class="cp-home-intro"><span>CHACHA PRIME MARKETPLACE</span><h2>Everything you need, in one place.</h2><p>Discover products from marketplace sellers, compare B2B and B2C pricing, join group buying campaigns and shop live.</p><a href="{{route('shop')}}">Explore the marketplace →</a></div><div class="cp-product-grid">@foreach($featured as $product)<a class="cp-product-card" href="{{route('product',$product->id)}}"><small>{{$product->category?->name ?? 'Featured'}}</small><h3>{{$product->name}}</h3><strong>{{$product->currency}} {{number_format((float)$product->retail_price,2)}}</strong><span>View product →</span></a>@endforeach</div></section>
@endsection