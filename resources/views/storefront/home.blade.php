@extends('layouts.storefront')

@section('title', 'Chacha Prime — Premium Global Marketplace')

@section('content')
<section class="hero">
    <div class="container hero-grid">
        <div>
            <span class="eyebrow">GLOBAL MULTI-VENDOR MARKETPLACE</span>
            <h1>Shop smarter. Sell globally. <span>Grow together.</span></h1>
            <p>Discover premium products, wholesale opportunities, group buying, live commerce and a trusted marketplace experience.</p>
            <div class="hero-actions">
                <a class="button button-dark" href="#">Explore marketplace</a>
                <a class="button button-light" href="{{ route('vendor.register') }}">Become a seller</a>
            </div>
        </div>
        <div class="hero-card">
            <div class="hero-card-top">CHACHA PRIME</div>
            <div class="hero-card-value">B2B + B2C</div>
            <div class="hero-card-meta">Global commerce platform</div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-heading">
            <div><span class="eyebrow">WHY CHACHA PRIME</span><h2>Built for modern commerce</h2></div>
        </div>
        <div class="feature-grid">
            <article class="feature-card"><h3>Multi-vendor</h3><p>Powerful seller tools, storefronts, order management and analytics.</p></article>
            <article class="feature-card"><h3>B2B + B2C</h3><p>Retail and tiered wholesale pricing with flexible customer discounts.</p></article>
            <article class="feature-card"><h3>Live commerce</h3><p>Interactive live shopping with product pinning and buying actions.</p></article>
            <article class="feature-card"><h3>AI powered</h3><p>Context-aware AI assistance for customers, sellers and administrators.</p></article>
        </div>
    </div>
</section>
@endsection
