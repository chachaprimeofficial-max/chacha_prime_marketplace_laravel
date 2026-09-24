@php
$footerLinks = [
    'Shop' => [
        ['All Products', route('shop')],
        ['Today\'s Deals', route('shop').'?deals=1'],
        ['Group Buying', route('group-buying')],
        ['Live Shopping', route('live')],
        ['B2B Marketplace', route('shop')],
        ['B2C Marketplace', route('shop')],
    ],
    'Sell on Chacha' => [
        ['Seller Center', auth()->user()?->role === 'vendor' ? route('vendor.dashboard') : route('vendor.register')],
        ['Vendor Registration', route('vendor.register')],
        ['Seller Policies', route('home').'#customer-service'],
        ['Vendor Support', route('home').'#customer-service'],
    ],
    'Customer Service' => [
        ['Your Orders', auth()->check() ? route('customer.orders') : route('auth.login')],
        ['Returns & Refunds', route('home').'#customer-service'],
        ['Shipping Information', route('home').'#customer-service'],
        ['Help Center', route('home').'#customer-service'],
        ['Contact Us', route('home').'#customer-service'],
    ],
    'Chacha Prime' => [
        ['About Chacha Prime', route('home').'#about'],
        ['Privacy Policy', route('home').'#privacy'],
        ['Terms & Conditions', route('home').'#terms'],
        ['Payment Methods', route('home').'#payments'],
        ['Affiliate & Partners', route('home').'#partners'],
    ],
];
@endphp

<footer class="cp-footer" id="customer-service">
    <div class="cp-footer-top">
        <div class="cp-footer-container cp-footer-top-grid">
            <div class="cp-footer-brand">
                <a href="{{route('home')}}" class="cp-footer-logo"><img src="{{asset('images/chacha-logo.svg')}}" alt="Chacha Prime"></a>
                <p>Premium B2B &amp; B2C marketplace for products, group buying, live shopping and trusted sellers.</p>
                <div class="cp-footer-trust"><span>Secure Checkout</span><span>Buyer Protection</span><span>7-Day Return Window</span></div>
            </div>
            <div class="cp-footer-links">
                @foreach($footerLinks as $heading => $links)
                    <div class="cp-footer-col">
                        <h3>{{$heading}}</h3>
                        @foreach($links as [$label,$url])<a href="{{$url}}">{{$label}}</a>@endforeach
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="cp-footer-payment" id="payments">
        <div class="cp-footer-container">
            <div class="cp-partner-heading">
                <div><strong>Payment Partners</strong><small>Accepted methods are shown according to your checkout and region.</small></div>
            </div>
            <div class="cp-logo-grid cp-payment-grid" aria-label="Payment partners">
                <div class="cp-brand-card"><svg viewBox="0 0 100 32" aria-label="Visa"><text x="2" y="25" font-family="Arial,sans-serif" font-style="italic" font-weight="900" font-size="27" fill="#1434CB">VISA</text></svg></div>
                <div class="cp-brand-card"><svg viewBox="0 0 120 40" aria-label="Mastercard"><circle cx="47" cy="20" r="15" fill="#EB001B"/><circle cx="73" cy="20" r="15" fill="#F79E1B"/><path d="M60 8a15 15 0 0 1 0 24 15 15 0 0 1 0-24Z" fill="#FF5F00"/><text x="4" y="37" font-family="Arial,sans-serif" font-size="9" font-weight="700" fill="#111827">mastercard</text></svg></div>
                <div class="cp-brand-card"><svg viewBox="0 0 150 36" aria-label="American Express"><rect x="3" y="3" width="144" height="30" rx="3" fill="#2EA3DB"/><text x="75" y="16" text-anchor="middle" font-family="Arial,sans-serif" font-size="10" font-weight="900" fill="#fff">AMERICAN</text><text x="75" y="27" text-anchor="middle" font-family="Arial,sans-serif" font-size="10" font-weight="900" fill="#fff">EXPRESS</text></svg></div>
                <div class="cp-brand-card"><svg viewBox="0 0 120 36" aria-label="PayPal"><text x="8" y="27" font-family="Arial,sans-serif" font-size="24" font-weight="900" font-style="italic" fill="#003087">Pay</text><text x="48" y="27" font-family="Arial,sans-serif" font-size="24" font-weight="900" font-style="italic" fill="#009CDE">Pal</text></svg></div>
                <div class="cp-brand-card"><svg viewBox="0 0 120 36" aria-label="Alipay"><rect x="4" y="6" width="112" height="24" rx="4" fill="#1677FF"/><text x="60" y="23" text-anchor="middle" font-family="Arial,sans-serif" font-size="15" font-weight="900" fill="#fff">Alipay</text></svg></div>
                <div class="cp-brand-card"><svg viewBox="0 0 135 36" aria-label="WeChat Pay"><circle cx="19" cy="18" r="12" fill="#07C160"/><circle cx="27" cy="22" r="9" fill="#07C160"/><circle cx="15" cy="16" r="2" fill="#fff"/><circle cx="23" cy="20" r="2" fill="#fff"/><text x="39" y="23" font-family="Arial,sans-serif" font-size="13" font-weight="800" fill="#07C160">WeChat Pay</text></svg></div>
                <div class="cp-brand-card"><svg viewBox="0 0 135 36" aria-label="easypaisa"><text x="67" y="24" text-anchor="middle" font-family="Arial,sans-serif" font-size="18" font-weight="800" fill="#1B1B1B">easypaisa</text><path d="M113 8c7 3 9 10 5 17" fill="none" stroke="#72BF44" stroke-width="4" stroke-linecap="round"/></svg></div>
                <div class="cp-brand-card"><svg viewBox="0 0 135 36" aria-label="JazzCash"><circle cx="22" cy="18" r="13" fill="#E31B23"/><text x="22" y="23" text-anchor="middle" font-family="Arial,sans-serif" font-size="8" font-weight="900" fill="#FFD200">J</text><text x="42" y="24" font-family="Arial,sans-serif" font-size="16" font-weight="900" fill="#E31B23">JazzCash</text></svg></div>
                <div class="cp-brand-card"><svg viewBox="0 0 110 36" aria-label="RAAST"><text x="55" y="24" text-anchor="middle" font-family="Arial,sans-serif" font-size="18" font-weight="900" fill="#1261A0">RAAST</text></svg></div>
                <div class="cp-brand-card"><svg viewBox="0 0 120 36" aria-label="UnionPay"><rect x="5" y="5" width="110" height="26" rx="3" fill="#e21836"/><text x="60" y="23" text-anchor="middle" font-family="Arial,sans-serif" font-size="12" font-weight="900" fill="#fff">UNIONPAY</text></svg></div>
                <div class="cp-brand-card"><svg viewBox="0 0 120 36" aria-label="Apple Pay"><rect x="5" y="5" width="110" height="26" rx="5" fill="#111"/><text x="60" y="23" text-anchor="middle" font-family="Arial,sans-serif" font-size="13" font-weight="800" fill="#fff">Apple Pay</text></svg></div>
                <div class="cp-brand-card"><svg viewBox="0 0 120 36" aria-label="Google Pay"><text x="60" y="23" text-anchor="middle" font-family="Arial,sans-serif" font-size="13" font-weight="900" fill="#4285F4">G</text><text x="74" y="23" font-family="Arial,sans-serif" font-size="12" font-weight="700" fill="#444">Pay</text></svg></div>
            </div>
        </div>
    </div>

    <div class="cp-footer-delivery">
        <div class="cp-footer-container">
            <div class="cp-partner-heading"><div><strong>Delivery &amp; Logistics Partners</strong><small>Courier availability depends on destination, seller and shipping method.</small></div></div>
            <div class="cp-logo-grid cp-delivery-grid">
                <div class="cp-brand-card cp-courier-tcs"><span class="courier-mark">TCS</span><small>Express</small></div>
                <div class="cp-brand-card cp-courier-leopards"><span class="courier-mark">Leopards</span><small>There for You</small></div>
                <div class="cp-brand-card cp-courier-dhl"><span class="courier-mark">DHL</span><small>Express</small></div>
                <div class="cp-brand-card cp-courier-blueex"><span class="courier-mark">blueEX</span><small>eCommerce Logistics</small></div>
                <div class="cp-brand-card cp-courier-postex"><span class="courier-mark">PostEx.</span><small>Delivery &amp; fintech</small></div>
                <div class="cp-brand-card cp-courier-trax"><span class="courier-mark">TRAX</span><small>Logistics</small></div>
                <div class="cp-brand-card cp-courier-mp"><span class="courier-mark">M&amp;P</span><small>Express Logistics</small></div>
                <div class="cp-brand-card cp-courier-fedex"><span class="courier-mark">FedEx</span><small>Express</small></div>
            </div>
        </div>
    </div>

    <div class="cp-footer-middle">
<div class="cp-footer-container cp-footer-badges">
<div class="cp-footer-badge"><b class="cp-svg-icon cp-lock-icon" aria-hidden="true"></b><span><strong>Encrypted checkout</strong><small>Your payment details are protected.</small></span></div>
<div class="cp-footer-badge"><b class="cp-svg-icon cp-shield-icon" aria-hidden="true"></b><span><strong>Buyer protection</strong><small>Order and seller controls built in.</small></span></div>
<div class="cp-footer-badge"><b class="cp-svg-icon cp-return-icon" aria-hidden="true"></b><span><strong>Easy returns</strong><small>7-day return window with evidence review.</small></span></div>
<div class="cp-footer-badge"><b class="cp-svg-icon cp-globe-footer-icon" aria-hidden="true"></b><span><strong>Worldwide commerce</strong><small>Multi-currency marketplace experience.</small></span></div>
</div></div>

<div class="cp-footer-bottom">
        <div class="cp-footer-container cp-footer-bottom-inner">
            <span>© {{date('Y')}} Chacha Prime. All rights reserved.</span>
            <div class="cp-footer-mini-links"><a href="{{route('home').'#privacy'}}">Privacy</a><a href="{{route('home').'#terms'}}">Terms</a><a href="{{route('home').'#customer-service'}}">Support</a></div>
            <span>Premium Marketplace • B2B • B2C</span>
        </div>
    </div>

    <style>
.cp-footer{margin-top:48px;color:#dbe4ee;background:#07111f;font-family:Inter,system-ui,sans-serif}.cp-footer-container{width:min(1440px,calc(100% - 40px));margin:0 auto}.cp-footer-top{padding:48px 0 42px;background:linear-gradient(180deg,#0a1727,#07111f)}.cp-footer-top-grid{display:flex;gap:46px}.cp-footer-brand{width:260px;flex:0 0 260px}.cp-footer-logo{display:inline-flex;margin-bottom:18px}.cp-footer-logo img{display:block;width:190px;height:auto;max-height:58px;object-fit:contain}.cp-footer-brand p{margin:0;color:#94a8bd;font-size:13px;line-height:1.8}.cp-footer-trust{display:flex;flex-direction:column;gap:8px;margin-top:20px;color:#c7d4e2;font-size:11px;font-weight:700}.cp-footer-links{flex:1;display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:28px}.cp-footer-col h3{margin:4px 0 15px;color:#fff;font-size:14px}.cp-footer-col a,.cp-footer-mini-links a{display:block;color:#9eb0c3;text-decoration:none;font-size:12px;line-height:2.05}.cp-footer-col a:hover,.cp-footer-mini-links a:hover{color:#fff}.cp-footer-payment,.cp-footer-delivery{background:#101d2d;border-top:1px solid #1e3043}.cp-footer-delivery{background:#0b1827;border-bottom:1px solid #1e3043}.cp-partner-heading{padding:20px 0 12px}.cp-partner-heading strong{display:block;color:#fff;font-size:14px}.cp-partner-heading small{display:block;color:#71869b;font-size:10px;margin-top:5px}.cp-logo-grid{display:grid;grid-template-columns:repeat(10,minmax(0,1fr));gap:8px;padding:0 0 20px}.cp-brand-card{height:42px;background:#fff;border:1px solid #d7e0e8;border-radius:7px;display:flex;align-items:center;justify-content:center;padding:5px 7px;box-shadow:0 2px 7px #00000012}.cp-brand-card svg{width:100%;height:100%;max-height:30px}.cp-delivery-grid{grid-template-columns:repeat(8,minmax(0,1fr))}.cp-courier-tcs{background:#d40000;color:#fff}.cp-courier-leopards{background:#ffc900;color:#111}.cp-courier-dhl{background:#ffcc00;color:#d40511}.cp-courier-blueex{background:#fff;color:#1670c5}.cp-courier-postex{background:#101010;color:#fff}.cp-courier-trax{background:#fff;color:#17252f}.cp-courier-mp{background:#fff;color:#0b5cab}.cp-courier-fedex{background:#fff;color:#4d148c}.courier-mark{font-size:17px;font-weight:950;letter-spacing:-.8px;line-height:1}.cp-brand-card small{display:block;font-size:6px;font-weight:800;opacity:.75;margin-top:3px}.cp-footer-middle{background:#091725}.cp-footer-badges{padding:22px 0;display:grid;grid-template-columns:repeat(4,1fr);gap:15px}.cp-footer-badge{display:flex;align-items:center;gap:11px;padding:13px;border:1px solid #1c3045;border-radius:10px;background:#0d1b2b}.cp-footer-badge>b{width:30px;height:30px;border-radius:50%;display:grid;place-items:center;background:#172b40;color:#fff;font-size:14px;flex:0 0 30px}.cp-footer-badge strong,.cp-footer-badge small{display:block}.cp-footer-badge strong{font-size:10px;color:#e6edf5}.cp-footer-badge small{font-size:9px;color:#71869b;margin-top:3px}.cp-svg-icon{position:relative}.cp-lock-icon:before{content:"";width:10px;height:9px;border:1.5px solid #fff;border-radius:2px;position:absolute;left:10px;top:12px}.cp-lock-icon:after{content:"";width:7px;height:7px;border:1.5px solid #fff;border-bottom:0;border-radius:7px 7px 0 0;position:absolute;left:11.5px;top:6px}.cp-shield-icon:before{content:"";position:absolute;left:9px;top:5px;width:12px;height:15px;border:1.5px solid #fff;border-radius:8px 8px 10px 10px;transform:rotate(45deg) scale(.8)}.cp-return-icon:before{content:"";position:absolute;left:6px;top:2px;font-size:22px;color:#fff}.cp-globe-footer-icon:before{content:"";position:absolute;left:8px;top:8px;width:14px;height:14px;border:1.5px solid #fff;border-radius:50%}.cp-globe-footer-icon:after{content:"";position:absolute;left:11px;right:11px;top:15px;border-top:1px solid #fff}.cp-footer-bottom{background:#050d17;border-top:1px solid #16283b}.cp-footer-bottom-inner{min-height:56px;align-items:center;justify-content:space-between;color:#71869b;font-size:10px;display:flex}.cp-footer-mini-links{display:flex;gap:18px}.cp-footer-mini-links a{line-height:1}@media(max-width:1050px){.cp-logo-grid{grid-template-columns:repeat(5,1fr)}.cp-delivery-grid{grid-template-columns:repeat(4,1fr)}}@media(max-width:900px){.cp-footer-container{width:min(100% - 28px,1440px)}.cp-footer-top-grid{display:block}.cp-footer-brand{width:220px;margin-bottom:30px}.cp-footer-links{grid-template-columns:repeat(2,1fr)}.cp-footer-badges{grid-template-columns:repeat(2,1fr)}}@media(max-width:620px){.cp-footer-top{padding:34px 0}.cp-footer-links{grid-template-columns:repeat(2,1fr);gap:25px 16px}.cp-logo-grid,.cp-delivery-grid{grid-template-columns:repeat(2,1fr)}.cp-footer-badges{grid-template-columns:1fr}.cp-footer-bottom-inner{min-height:auto;padding:14px 0;flex-direction:column;align-items:flex-start;gap:10px}.cp-footer-mini-links{gap:14px}}
</style>
</footer>