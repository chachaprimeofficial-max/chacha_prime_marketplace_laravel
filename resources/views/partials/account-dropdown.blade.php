<div class="cp-account-dropdown" data-cp-account-menu>
    @guest
        <div class="cp-account-head"><strong>Welcome to Chacha Prime</strong></div>
        <a class="cp-signin" href="{{ route('auth.login') }}">Sign in</a>
        <p>New customer? <a href="{{ route('auth.register') }}">Start here.</a></p>
    @else
        <div class="cp-account-head"><strong>Hello, {{ $user->name }}</strong></div>
        <a href="{{ route('customer.dashboard') }}">Your Account</a>
        <a href="{{ route('customer.orders') }}">Your Orders</a>
        <a href="{{ route('customer.wishlist') }}">Your Wishlist</a>
        @if($role === 'vendor')
            <a href="{{ route('vendor.dashboard') }}">Vendor Dashboard</a>
        @else
            <a href="{{ route('vendor.register') }}">Sell on Chacha Prime</a>
        @endif
        @if(in_array($role, ['admin','super_admin','staff'], true))
            <a href="{{ route('admin.dashboard') }}">Admin Panel</a>
        @endif
        <form method="POST" action="{{ route('auth.logout') }}">
            @csrf
            <button type="submit">Sign Out</button>
        </form>
    @endguest
</div>