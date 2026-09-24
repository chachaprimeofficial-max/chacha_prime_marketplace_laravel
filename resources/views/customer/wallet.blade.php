@extends('layouts.customer')
@section('title','Wallet — Chacha Prime')
@push('styles')<style>
.cp-wallet-grid{display:grid;grid-template-columns:1fr 1.4fr;gap:15px}.cp-wallet-balance{padding:24px;border-radius:18px;background:linear-gradient(135deg,#0b1220,#243b53);color:#fff;min-height:180px;box-shadow:0 18px 40px rgba(15,23,42,.16)}.cp-wallet-label{font-size:9px;letter-spacing:1.6px;color:#aebdcb}.cp-wallet-amount{font-size:34px;font-weight:900;margin-top:12px}.cp-wallet-status{display:inline-flex;margin-top:14px;padding:5px 8px;border-radius:999px;background:#ffffff18;font-size:9px}.cp-wallet-row{display:flex;justify-content:space-between;gap:12px;padding:13px 0;border-bottom:1px solid #eef2f7}.cp-wallet-row:last-child{border-bottom:0}.cp-wallet-row strong{font-size:11px}.cp-wallet-row small{display:block;color:#94a3b8;font-size:9px;margin-top:3px}.cp-wallet-credit{color:#047857}.cp-wallet-debit{color:#b91c1c}@media(max-width:850px){.cp-wallet-grid{grid-template-columns:1fr}}
</style>@endpush
@section('content')
<h1 class="cp-customer-page-title">Wallet</h1>
<div class="cp-wallet-grid">
<section class="cp-wallet-balance"><div class="cp-wallet-label">AVAILABLE MARKETPLACE BALANCE</div><div class="cp-wallet-amount">{{number_format((float)($wallet->balance??0),2)}} {{strtoupper($wallet->currency??'USD')}}</div><span class="cp-wallet-status">{{strtoupper($wallet->status??'active')}}</span></section>
<section class="cp-customer-panel"><h2 style="margin:0 0 8px;font-size:17px">Transaction history</h2>@forelse($transactions as $t)<div class="cp-wallet-row"><div><strong>{{$t->description ?: ucfirst($t->type)}}</strong><small>{{$t->created_at}} @if($t->reference_type) · {{$t->reference_type}} #{{$t->reference_id}} @endif</small></div><strong class="{{in_array(strtolower($t->type),['credit','refund','deposit'])?'cp-wallet-credit':'cp-wallet-debit'}}">{{in_array(strtolower($t->type),['credit','refund','deposit'])?'+':'-'}}{{number_format(abs((float)$t->amount),2)}}</strong></div>@empty<p class="cp-customer-muted">No wallet transactions yet.</p>@endforelse</section>
</div>
@endsection