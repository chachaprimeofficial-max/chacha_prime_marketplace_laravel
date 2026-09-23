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
        <div class="cp-footer-container">
            <div class="cp-footer-brand">
                <a href="{{route('home')}}" class="cp-footer-logo">
                    <img src="{{asset('images/chacha-logo.svg')}}" alt="Chacha Prime">
                </a>
                <p>Premium B2B &amp; B2C marketplace for products, group buying, live shopping and trusted sellers.</p>
                <div class="cp-footer-trust">
                    <span>✓ Secure Checkout</span>
                    <span>✓ Buyer Protection</span>
                    <span>✓ 30-Day Returns</span>
                </div>
            </div>

            <div class="cp-footer-links">
                @foreach($footerLinks as $heading => $links)
                    <div class="cp-footer-col">
                        <h3>{{$heading}}</h3>
                        @foreach($links as [$label,$url])
                            <a href="{{$url}}">{{$label}}</a>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="cp-footer-payment">
        <div class="cp-footer-container cp-payment-row">
            <div>
                <strong>Secure payment methods</strong>
                <span class="cp-payment-note">Payment availability may vary by country, currency and checkout.</span>
            </div>
            <div class="cp-payment-icons" aria-label="Accepted payment methods">
                <span class="cp-pay-icon"><img src="https://cdn.simpleicons.org/visa" alt="Visa"></span>
                <span class="cp-pay-icon"><img src="https://cdn.simpleicons.org/mastercard" alt="Mastercard"></span>
                <span class="cp-pay-icon"><img src="https://cdn.simpleicons.org/americanexpress" alt="American Express"></span>
                <span class="cp-pay-icon"><img src="https://cdn.simpleicons.org/paypal" alt="PayPal"></span>
                <span class="cp-pay-icon"><img src="https://cdn.simpleicons.org/alipay" alt="Alipay"></span>
                <span class="cp-pay-icon"><img src="https://cdn.simpleicons.org/wechat" alt="WeChat Pay"></span>
                <span class="cp-pay-text cp-ep">easypaisa</span>
                <span class="cp-pay-text cp-jc">JazzCash</span>
                <span class="cp-pay-text cp-raast">RAAST</span>
                <span class="cp-pay-text cp-bank">BANK</span>
            </div>
        </div>
    </div>

    <div class="cp-footer-middle">
        <div class="cp-footer-container cp-footer-badges">
            <div class="cp-footer-badge"><b>🔒</b><span><strong>Encrypted checkout</strong><small>Your payment details are protected.</small></span></div>
            <div class="cp-footer-badge"><b>✓</b><span><strong>Verified marketplace</strong><small>Seller and order controls built in.</small></span></div>
            <div class="cp-footer-badge"><b>↻</b><span><strong>Easy returns</strong><small>30-day refund / replacement policy.</small></span></div>
            <div class="cp-footer-badge"><b>◎</b><span><strong>Worldwide commerce</strong><small>Multi-currency marketplace experience.</small></span></div>
        </div>
    </div>

    <div class="cp-footer-bottom">
        <div class="cp-footer-container cp-footer-bottom-inner">
            <span>© {{date('Y')}} Chacha Prime. All rights reserved.</span>
            <div class="cp-footer-mini-links">
                <a href="{{route('home').'#privacy'}}">Privacy</a>
                <a href="{{route('home').'#terms'}}">Terms</a>
                <a href="{{route('home').'#customer-service'}}">Support</a>
            </div>
            <span>Premium Marketplace • B2B • B2C</span>
        </div>
    </div>

    <style>
        .cp-footer{margin-top:48px;color:#dbe4ee;background:#07111f;font-family:Inter,system-ui,sans-serif}
        .cp-footer-container{width:min(1440px,calc(100% - 40px));margin:0 auto}
        .cp-footer-top{padding:48px 0 42px;background:linear-gradient(180deg,#0a1727,#07111f)}
        .cp-footer-container{display:flex;gap:46px}
        .cp-footer-brand{width:260px;flex:0 0 260px}
        .cp-footer-logo{display:inline-flex;align-items:center;margin-bottom:18px}
        .cp-footer-logo img{display:block;width:190px;height:auto;max-height:58px;object-fit:contain;object-position:left center}
        .cp-footer-brand p{margin:0;color:#94a8bd;font-size:13px;line-height:1.8}
        .cp-footer-trust{display:flex;flex-direction:column;gap:8px;margin-top:20px;color:#c7d4e2;font-size:11px;font-weight:700}
        .cp-footer-links{flex:1;display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:28px}
        .cp-footer-col h3{margin:4px 0 15px;color:#fff;font-size:14px}
        .cp-footer-col a,.cp-footer-mini-links a{display:block;color:#9eb0c3;text-decoration:none;font-size:12px;line-height:2.05;transition:.18s}
        .cp-footer-col a:hover,.cp-footer-mini-links a:hover{color:#fff}
        .cp-footer-payment{background:#101d2d;border-top:1px solid #1e3043;border-bottom:1px solid #1e3043}
        .cp-payment-row{align-items:center;justify-content:space-between;padding:20px 0;gap:25px}
        .cp-payment-row strong{display:block;color:#fff;font-size:13px}
        .cp-payment-note{display:block;color:#758ba1;font-size:10px;margin-top:5px}
        .cp-payment-icons{display:flex;align-items:center;justify-content:flex-end;flex-wrap:wrap;gap:8px}
        .cp-pay-icon,.cp-pay-text{height:34px;min-width:52px;padding:5px 10px;display:inline-flex;align-items:center;justify-content:center;background:#fff;border:1px solid #d8e0e8;border-radius:6px;box-sizing:border-box}
        .cp-pay-icon img{max-width:48px;max-height:22px;width:auto;height:auto;display:block}
        .cp-pay-text{font-size:8px;font-weight:900;letter-spacing:.1px;background:#fff}
        .cp-ep{color:#009b52}.cp-jc{color:#ef7d00}.cp-raast{color:#0b5cab}.cp-bank{color:#26364a}
        .cp-footer-middle{background:#091725}
        .cp-footer-badges{padding:22px 0;display:grid;grid-template-columns:repeat(4,1fr);gap:15px}
        .cp-footer-badge{display:flex;align-items:center;gap:11px;padding:13px;border:1px solid #1c3045;border-radius:10px;background:#0d1b2b}
        .cp-footer-badge>b{width:30px;height:30px;border-radius:50%;display:grid;place-items:center;background:#172b40;color:#fff;font-size:14px;flex:0 0 30px}
        .cp-footer-badge strong,.cp-footer-badge small{display:block}
        .cp-footer-badge strong{font-size:10px;color:#e6edf5}.cp-footer-badge small{font-size:9px;color:#71869b;margin-top:3px}
        .cp-footer-bottom{background:#050d17;border-top:1px solid #16283b}
        .cp-footer-bottom-inner{min-height:56px;align-items:center;justify-content:space-between;color:#71869b;font-size:10px}
        .cp-footer-mini-links{display:flex;gap:18px}
        .cp-footer-mini-links a{line-height:1}
        @media(max-width:900px){
            .cp-footer-container{width:min(100% - 28px,1440px)}
            .cp-footer-brand{width:220px;flex-basis:220px}
            .cp-footer-links{grid-template-columns:repeat(2,1fr)}
            .cp-payment-row{align-items:flex-start;flex-direction:column}
            .cp-payment-icons{justify-content:flex-start}
            .cp-footer-badges{grid-template-columns:repeat(2,1fr)}
            .cp-footer-bottom-inner{padding:14px 0;flex-wrap:wrap;gap:10px}
        }
        @media(max-width:620px){
            .cp-footer-top{padding:34px 0}
            .cp-footer-container{display:block}
            .cp-footer-brand{width:auto;margin-bottom:30px}
            .cp-footer-links{display:grid;grid-template-columns:repeat(2,1fr);gap:25px 16px}
            .cp-footer-payment .cp-footer-container{display:block}
            .cp-payment-icons{margin-top:14px}
            .cp-footer-badges{display:grid;grid-template-columns:1fr;gap:9px}
            .cp-footer-bottom-inner{display:flex;flex-direction:column;align-items:flex-start}
        }
    </style>
</footer>