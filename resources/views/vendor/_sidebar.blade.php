<aside class="cp-vendor-sidebar">
  <div class="cp-vendor-brand">
    <a href="{{route('home')}}" class="cp-vendor-logo-link"><img src="{{asset('images/chacha-logo.svg')}}" alt="CHACHA 查查 Prime"></a>
    <div class="cp-vendor-brand-title">Seller Center</div>
    <div class="cp-vendor-brand-sub">Marketplace management</div>
  </div>
  <nav class="cp-vendor-nav">
    <a class="{{request()->routeIs('vendor.dashboard')?'is-active':''}}" href="{{route('vendor.dashboard')}}">▦ <span>Overview</span></a>
    <a class="{{request()->routeIs('vendor.products*')?'is-active':''}}" href="{{route('vendor.products')}}">▣ <span>Products</span></a>
    <a class="{{request()->routeIs('vendor.orders*')?'is-active':''}}" href="{{route('vendor.orders')}}">▤ <span>Orders</span></a>
    <a class="{{request()->routeIs('vendor.inventory')?'is-active':''}}" href="{{route('vendor.inventory')}}">◫ <span>Inventory</span></a>
    <a class="{{request()->routeIs('vendor.pricing*')?'is-active':''}}" href="{{route('vendor.pricing')}}">◈ <span>B2B / B2C Pricing</span></a>
    <a class="{{request()->routeIs('vendor.analytics')?'is-active':''}}" href="{{route('vendor.analytics')}}">◒ <span>Analytics</span></a>
    <a class="{{request()->routeIs('vendor.account-health')?'is-active':''}}" href="{{route('vendor.account-health')}}">♥ <span>Account Health</span></a>
    <a class="{{request()->routeIs('vendor.reports')?'is-active':''}}" href="{{route('vendor.reports')}}">▤ <span>Reports</span></a>
    <a class="{{request()->routeIs('vendor.group-buying*')?'is-active':''}}" href="{{route('vendor.group-buying')}}">◎ <span>Group Buying</span></a>
    <a class="{{request()->routeIs('vendor.live-commerce*')?'is-active':''}}" href="{{route('vendor.live-commerce')}}">▶ <span>Live Commerce</span></a>
    <a class="{{request()->routeIs('vendor.coupons*')?'is-active':''}}" href="{{route('vendor.coupons')}}">◇ <span>Coupons</span></a>
    <a class="{{request()->routeIs('vendor.shipping')?'is-active':''}}" href="{{route('vendor.shipping')}}">⌁ <span>Shipping</span></a>
    <a class="{{request()->routeIs('vendor.wallet')?'is-active':''}}" href="{{route('vendor.wallet')}}">▱ <span>Wallet</span></a>
    <a class="{{request()->routeIs('vendor.payouts*')?'is-active':''}}" href="{{route('vendor.payouts')}}">↗ <span>Payouts</span></a>
    <a class="{{request()->routeIs('vendor.returns*')?'is-active':''}}" href="{{route('vendor.returns')}}">↩ <span>Returns & Refunds</span></a>
    <a class="{{request()->routeIs('vendor.messages*')?'is-active':''}}" href="{{route('vendor.messages')}}">✉ <span>Customer Messages</span></a>
    <a class="{{request()->routeIs('vendor.reviews')?'is-active':''}}" href="{{route('vendor.reviews')}}">★ <span>Reviews</span></a>
    <a class="{{request()->routeIs('vendor.ai')?'is-active':''}}" href="{{route('vendor.ai')}}">✦ <span>AI Assistant</span></a>
  </nav>
  <div class="cp-vendor-help">Need help? <a href="{{route('home')}}">Open Storefront</a> or contact Chacha Prime support.</div>
</aside>