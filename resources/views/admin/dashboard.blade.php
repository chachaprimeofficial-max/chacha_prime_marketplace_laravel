@extends('layouts.storefront')
@section('title', 'Admin Dashboard — Chacha Prime')
@section('content')
<div class="container dashboard-shell">
    <div class="dashboard-heading"><div><span class="eyebrow">CONTROL CENTER</span><h1>Marketplace dashboard</h1></div><span class="status-pill">System online</span></div>
    <div class="metric-grid">
        <div class="metric-card"><span>Orders</span><strong>0</strong><small>Ready for live data</small></div>
        <div class="metric-card"><span>Vendors</span><strong>0</strong><small>Onboarding pipeline</small></div>
        <div class="metric-card"><span>Revenue</span><strong>—</strong><small>Connect payment reporting</small></div>
        <div class="metric-card"><span>AI Assistant</span><strong>Ready</strong><small>Gemini configuration pending</small></div>
    </div>
</div>
@endsection
