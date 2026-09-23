@php
    $cart = session('cart', []);
    $cartCount = collect($cart)->sum(fn($qty) => (int) $qty);
    $user = auth()->user();
    $role = $user?->role;
@endphp

<header class="cp-header">
    <div class="cp-topbar">
        <div class="cp-top-inner">
            <div class="cp-mobile-menu-wrap">
                <button type="button" class="cp-icon-btn cp-mobile-menu-btn" data-cp-open-drawer aria-label="Open categories">☰</button>
            </div>

            <a class="cp-logo" href="{{ route('home') }}" aria-label="Chacha Prime home">CHACHA <span>PRIME</span></a>

            <a class="cp-deliver" href="#" aria-label="Delivery location">
                <span class="cp-pin">⌖</span>
                <span><small>Deliver to</small><strong>Pakistan</strong></span>
            </a>

            @include('partials.search-bar')

            <div class="cp-header-actions">
                <button type="button" class="cp-lang" data-cp-lang aria-label="Language selector">
                    <span>🇬🇧</span> EN <span class="cp-chevron">⌄</span>
                </button>

                <div class="cp-account-wrap">
                    <button type="button" class="cp-account" data-cp-account aria-expanded="false">
                        <small>Hello, {{ $user?->name ? ' '.Str::limit($user->name, 16) : 'sign in' }}</small>
                        <strong>Account &amp; Lists <span>⌄</span></strong>
                    </button>
                    @include('partials.account-dropdown')
                </div>

                <a class="cp-returns" href="{{ $user ? route('customer.orders') : route('auth.login') }}">
                    <small>Returns</small><strong>&amp; Orders</strong>
                </a>

                <a class="cp-cart" href="{{ route('cart') }}" aria-label="Cart">
                    <span class="cp-cart-icon">🛒</span>
                    <b>{{ $cartCount }}</b>
                    <strong>Cart</strong>
                </a>
            </div>
        </div>
    </div>

    <nav class="cp-subbar" aria-label="Main navigation">
        <div class="cp-sub-inner">
            <button type="button" class="cp-nav-link cp-all-btn" data-cp-open-drawer>☰ <strong>All</strong></button>
            <a class="cp-nav-link" href="{{ route('customer.live-shopping') }}">▶ Live Streams</a>
            <a class="cp-nav-link" href="{{ route('shop') }}?deals=1">🏷 Coupons</a>
            <a class="cp-nav-link" href="{{ route('home') }}#customer-service">Customer Service</a>
            <a class="cp-nav-link" href="{{ route('shop') }}?deals=1">Today's Deals</a>
            <a class="cp-nav-link" href="{{ route('home') }}#registry">Registry</a>
            <a class="cp-nav-link" href="{{ route('home') }}#gift-cards">Gift Cards</a>
            @if($role === 'vendor')
                <a class="cp-nav-link" href="{{ route('vendor.dashboard') }}">Sell with us</a>
            @else
                <a class="cp-nav-link" href="{{ route('vendor.register') }}">Sell with us</a>
            @endif
            <span class="cp-promo">Premium marketplace • B2B &amp; B2C</span>
        </div>
    </nav>
</header>

@include('partials.mega-menu')
