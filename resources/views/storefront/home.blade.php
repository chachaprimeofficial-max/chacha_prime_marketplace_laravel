@extends('layouts.storefront')
@section('title','Chacha Prime — Premium Marketplace')
@section('content')
<section class="dashboard-shell"><div class="dashboard-hero"><span class="eyebrow">GLOBAL MULTI-VENDOR MARKETPLACE</span><h1>Shop smarter. Sell globally.</h1><p>Chacha Prime connects retail buyers, B2B buyers and verified sellers with secure commerce, group buying, live shopping and flexible marketplace services.</p><div style="margin-top:25px;display:flex;gap:12px;flex-wrap:wrap"><a class="button button-dark" style="background:#fff;color:#111" href="{{ route('auth.register') }}">Start shopping</a><a class="button" style="background:#292d33;color:#fff" href="{{ route('vendor.register') }}">Become a seller</a></div></div></section>
@endsection