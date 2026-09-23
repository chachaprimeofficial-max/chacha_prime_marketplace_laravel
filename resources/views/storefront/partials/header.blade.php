<header class="site-header">
    <div class="container header-inner">
        <a class="brand" href="{{ route('home') }}">CHACHA PRIME</a>
        <nav class="nav">
            <a href="{{ route('home') }}">Home</a>
            <a href="#">Categories</a>
            <a href="#">Group Buying</a>
            <a href="#">Live</a>
            <a href="#">Deals</a>
        </nav>
        <div class="header-actions">
            <a href="{{ route('auth.login') }}">Login</a>
            <a class="button button-dark" href="{{ route('auth.register') }}">Create account</a>
        </div>
    </div>
</header>
