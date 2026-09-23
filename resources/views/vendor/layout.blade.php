<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>@yield('title','Seller Center — Chacha Prime')</title><link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet"><link rel="stylesheet" href="{{asset('css/header.css')}}">@stack('styles')
<style>
.cp-vendor-app{min-height:calc(100vh - 104px);background:#f5f7fa;display:grid;grid-template-columns:252px minmax(0,1fr);gap:0}
.cp-vendor-sidebar{position:sticky;top:104px;height:calc(100vh - 104px);overflow:auto;background:#0b1220;color:#fff;padding:16px 12px;border-right:1px solid #1f2937;z-index:20}
.cp-vendor-brand{padding:4px 8px 16px;border-bottom:1px solid #ffffff16;margin-bottom:10px}
.cp-vendor-logo-link{display:block;width:100%;height:58px;overflow:hidden}.cp-vendor-logo-link img{width:190px;height:58px;display:block;object-fit:contain;object-position:left center}
.cp-vendor-brand-title{font-size:18px;font-weight:900;margin-top:8px}.cp-vendor-brand-sub{font-size:11px;color:#94a3b8;margin-top:3px}
.cp-vendor-nav{display:grid;gap:3px}.cp-vendor-nav a{display:flex;align-items:center;gap:10px;color:#cbd5e1;text-decoration:none;padding:9px 10px;border-radius:9px;font-size:14px;font-weight:800;transition:.15s}
.cp-vendor-nav a:hover{background:#ffffff0d;color:#fff}.cp-vendor-nav a.is-active{background:#172554;color:#fff;box-shadow:inset 3px 0 #f59e0b}
.cp-vendor-help{margin:14px 4px 0;padding:11px;border:1px solid #ffffff12;background:#ffffff08;border-radius:10px;color:#94a3b8;font-size:10px;line-height:1.5}.cp-vendor-help a{color:#fbbf24;font-weight:800;text-decoration:none}
.cp-vendor-main{min-width:0;min-height:calc(100vh - 104px)}
@media(max-width:1050px){.cp-vendor-app{grid-template-columns:1fr}.cp-vendor-sidebar{position:sticky;top:104px;height:auto;max-height:210px;border-right:0;border-bottom:1px solid #1f2937;padding:10px}.cp-vendor-brand{display:none}.cp-vendor-nav{display:flex;overflow:auto;gap:4px}.cp-vendor-nav a{white-space:nowrap}.cp-vendor-help{display:none}}
</style></head><body>@include('layouts.header')<div class="cp-vendor-app"><aside class="cp-vendor-sidebar">
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
</aside><main class="cp-vendor-main">@yield('content')</main></div><script src="{{asset('js/header.js')}}" defer></script>@stack('scripts')</body></html>