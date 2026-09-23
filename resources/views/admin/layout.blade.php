<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>@yield('title','Admin — Chacha Prime')</title>
<style>
*{box-sizing:border-box}html{scroll-behavior:smooth}body{margin:0;font-family:Inter,ui-sans-serif,system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;background:#f5f7fb;color:#0f172a}.admin{display:flex;min-height:100vh}.side{width:264px;background:#0b1220;color:#fff;padding:18px 14px;position:fixed;inset:0 auto 0 0;z-index:50;overflow:auto;border-right:1px solid #ffffff0d}.brand{padding:12px 12px 18px;border-bottom:1px solid #ffffff10;margin-bottom:12px}.brand small{display:block;color:#94a3b8;font-size:9px;font-weight:900;letter-spacing:2px}.brand strong{display:block;font-size:20px;margin-top:4px;letter-spacing:-.4px}.brand span{display:inline-flex;margin-top:8px;padding:4px 7px;border-radius:999px;background:#f59e0b;color:#111827;font-size:9px;font-weight:900}.nav-title{padding:12px 12px 6px;color:#64748b;font-size:9px;font-weight:900;letter-spacing:1.4px;text-transform:uppercase}.side a{display:flex;align-items:center;gap:10px;padding:10px 11px;margin:3px 0;border-radius:10px;color:#cbd5e1;text-decoration:none;font-size:12px;font-weight:700}.side a:hover{background:#ffffff0d;color:#fff}.side a.active{background:#f59e0b;color:#111827}.side .store{margin-top:14px;background:#ffffff08;border:1px solid #ffffff12}.main{margin-left:264px;width:calc(100% - 264px);min-width:0;padding:20px 24px 35px}.topbar{position:sticky;top:12px;z-index:30;background:#fffffff2;backdrop-filter:blur(14px);border:1px solid #e5e7eb;border-radius:16px;padding:13px 17px;display:flex;justify-content:space-between;align-items:center;gap:16px;box-shadow:0 8px 30px #0f172a0a}.top-left{display:flex;align-items:center;gap:11px}.top-left strong{font-size:15px}.crumb{color:#94a3b8;font-size:11px}.userbox{display:flex;align-items:center;gap:10px}.avatar{width:34px;height:34px;border-radius:10px;background:#111827;color:#fff;display:grid;place-items:center;font-size:11px;font-weight:900}.userbox small{display:block;color:#64748b;font-size:10px}.userbox b{font-size:12px}.mobile-toggle{display:none;border:0;background:#111827;color:#fff;border-radius:9px;padding:8px 10px}.content{margin-top:18px}.panel{background:#fff;border:1px solid #e5e7eb;border-radius:18px;padding:20px;box-shadow:0 8px 30px #0f172a06}.admin-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px}.admin-stat{background:#fff;border:1px solid #e5e7eb;border-radius:16px;padding:17px;box-shadow:0 8px 25px #0f172a06}.admin-stat .label{color:#64748b;font-size:10px;font-weight:800}.admin-stat strong{display:block;font-size:25px;margin-top:8px;letter-spacing:-.5px}.admin-stat small{display:block;color:#94a3b8;font-size:10px;margin-top:5px}.admin-stat.warn strong{color:#b45309}.admin-stat.money strong{color:#047857}.section-title{display:flex;align-items:end;justify-content:space-between;gap:12px;margin:24px 0 11px}.section-title h2{margin:0;font-size:17px;letter-spacing:-.3px}.section-title p{margin:4px 0 0;color:#64748b;font-size:11px}.section-title a{color:#b45309;text-decoration:none;font-size:11px;font-weight:800}.quick-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:10px}.quick{background:#fff;border:1px solid #e5e7eb;border-radius:13px;padding:14px;color:#111827;text-decoration:none;transition:.16s}.quick:hover{border-color:#f59e0b;background:#fffbeb;transform:translateY(-2px)}.quick b{display:block;font-size:12px}.quick span{display:block;color:#64748b;font-size:10px;margin-top:4px}.quick em{float:right;font-style:normal;color:#f59e0b}.dashboard-two{display:grid;grid-template-columns:1.35fr 1fr;gap:14px;margin-top:14px}.table-wrap{overflow:auto}table{width:100%;border-collapse:collapse;min-width:500px}th,td{padding:11px 8px;border-bottom:1px solid #eef0f3;text-align:left;font-size:11px}th{font-size:9px;text-transform:uppercase;letter-spacing:.7px;color:#64748b}.status{display:inline-flex;padding:4px 7px;border-radius:999px;background:#f1f5f9;color:#475569;font-size:9px;font-weight:800;text-transform:capitalize}.notice{padding:12px;background:#ecfdf3;color:#027a48;border:1px solid #abefc6;border-radius:10px;margin-bottom:14px;font-size:11px;font-weight:700}.menu-overlay{display:none}
@media(max-width:1150px){.admin-grid{grid-template-columns:repeat(2,1fr)}.quick-grid{grid-template-columns:repeat(3,1fr)}}@media(max-width:900px){.side{transform:translateX(-100%);transition:.2s}.side.open{transform:translateX(0)}.main{margin-left:0;width:100%;padding:14px}.mobile-toggle{display:inline-block}.menu-overlay{position:fixed;inset:0;background:#02061780;z-index:40}.menu-overlay.show{display:block}.dashboard-two{grid-template-columns:1fr}}@media(max-width:650px){.admin-grid{grid-template-columns:1fr 1fr}.quick-grid{grid-template-columns:1fr 1fr}.topbar{top:6px}.userbox div{display:none}}@media(max-width:430px){.admin-grid{grid-template-columns:1fr}.quick-grid{grid-template-columns:1fr}.main{padding:10px}.panel{padding:14px}}
</style>
</head>
<body>
<div class="admin">
<aside class="side" id="adminSidebar">
<div class="brand"><a href="{{route('home')}}" style="display:block"><img src="{{asset('images/chacha-logo.svg')}}" alt="CHACHA 查查 Prime" style="width:185px;max-width:100%;height:48px;object-fit:contain;object-position:left center"></a><strong>Admin Control Center</strong><span>MARKETPLACE HQ</span></div>
<div class="nav-title">Overview</div>
<a class="{{request()->routeIs('admin.dashboard')?'active':''}}" href="{{route('admin.dashboard')}}">▦ <span>Dashboard</span></a>
<div class="nav-title">Commerce</div>
<a class="{{request()->routeIs('admin.users')?'active':''}}" href="{{route('admin.users')}}">♙ <span>Users & Customers</span></a>
<a class="{{request()->routeIs('admin.vendors')?'active':''}}" href="{{route('admin.vendors')}}">◈ <span>Vendors</span></a>
<a class="{{request()->routeIs('admin.products')?'active':''}}" href="{{route('admin.products')}}">▣ <span>Products</span></a>
<a class="{{request()->routeIs('admin.catalog-tools')?'active':''}}" href="{{route('admin.catalog-tools')}}">✦ <span>Catalog AI & Import</span></a>
<a class="{{request()->routeIs('admin.scan-center*')?'active':''}}" href="{{route('admin.scan-center')}}">▦ <span>Scan & Lookup</span></a>
<a class="{{request()->routeIs('admin.categories')?'active':''}}" href="{{route('admin.categories')}}">☷ <span>Categories</span></a>
<a class="{{request()->routeIs('admin.orders')?'active':''}}" href="{{route('admin.orders')}}">▤ <span>Orders</span></a>
<a class="{{request()->routeIs('admin.payments')?'active':''}}" href="{{route('admin.payments')}}">◇ <span>Payments</span></a>
<div class="nav-title">Marketplace</div>
<a href="{{route('admin.module','group-buying')}}">◎ <span>Group Buying</span></a>
<a href="{{route('admin.module','live-commerce')}}">▶ <span>Live Commerce</span></a>
<a href="{{route('admin.module','shipping')}}">⌁ <span>Shipping</span></a>
<a href="{{route('admin.module','brands')}}">◆ <span>Brands</span></a>
<a href="{{route('admin.module','coupons')}}">◇ <span>Coupons</span></a>
<div class="nav-title">Finance & System</div>
<a href="{{route('admin.wallets')}}">▱ <span>Wallets</span></a>
<a href="{{route('admin.cards')}}">▤ <span>Virtual Cards</span></a>
<a href="{{route('admin.staff')}}">⚙ <span>Staff & Permissions</span></a>
<a href="{{route('admin.module','currencies')}}">¤ <span>Currencies</span></a>
<a href="{{route('admin.module','payment-methods')}}">◉ <span>Payment Methods</span></a>
<a href="{{route('admin.module','settings')}}">⚙ <span>Settings</span></a>
<a href="{{route('admin.module','pages')}}">▤ <span>CMS Pages</span></a>
<a href="{{route('admin.module','ai-logs')}}">✦ <span>AI Activity</span></a>
<a href="{{route('admin.audit-logs')}}">◌ <span>Audit Logs</span></a>
<a class="store" href="{{route('home')}}">↗ <span>Open Storefront</span></a>
</aside>
<div class="menu-overlay" id="adminOverlay"></div>
<main class="main">
<header class="topbar">
<div class="top-left"><button class="mobile-toggle" id="adminMenuBtn">☰</button><div><span class="crumb">Chacha Prime / </span><strong>@yield('page_heading','Control Center')</strong></div></div>
<div class="userbox"><div><small>Signed in as</small><b>{{auth()->user()->name ?? 'Administrator'}}</b></div><span class="avatar">{{strtoupper(substr(auth()->user()->name ?? 'A',0,1))}}</span></div>
</header>
<div class="content">@yield('content')</div>
</main>
</div>
<script>
const btn=document.getElementById('adminMenuBtn'),side=document.getElementById('adminSidebar'),overlay=document.getElementById('adminOverlay');
function closeAdminMenu(){side?.classList.remove('open');overlay?.classList.remove('show')}
btn?.addEventListener('click',()=>{side?.classList.toggle('open');overlay?.classList.toggle('show')});
overlay?.addEventListener('click',closeAdminMenu);
</script>
</body>
</html>