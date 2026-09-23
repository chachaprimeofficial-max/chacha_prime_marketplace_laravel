@extends('admin.layout')
@section('title','Scan Center — Chacha Prime')
@section('page_heading','Global Scan & Lookup Center')
@section('content')
<div class="panel">
<h2>Scan or Search</h2><p style="color:#64748b;font-size:11px">Lookup Product ID, SKU, internal barcode, QR token or Order Number.</p>
<form method="GET" action="{{route('admin.scan-center.lookup')}}" style="display:flex;gap:10px;flex-wrap:wrap">
<input id="lookup" name="q" value="{{request('q',$query??'')}}" placeholder="CP-PROD-00000001 / SKU / Barcode / Order No." style="flex:1;min-width:250px">
<button style="padding:11px 16px;border:0;border-radius:9px;background:#111827;color:#fff">Search</button>
<button type="button" id="barcodeBtn" style="padding:11px 16px;border:1px solid #cbd5e1;border-radius:9px;background:#fff">📷 Scan</button>
</form>
<div id="scanner" style="display:none;margin-top:15px;padding:15px;background:#f8fafc;border-radius:12px"><div id="scannerHint">Camera barcode/QR scanning can be enabled with a browser-compatible scanner library.</div><button type="button" id="closeScanner">Close</button></div>
</div>
@if(isset($product) || isset($order))
<div class="dashboard-two" style="margin-top:14px">
@if($product)<section class="panel"><h2>Product Found</h2><p><b>{{$product->name}}</b></p><p>Product ID: <b>{{$product->product_code}}</b></p><p>SKU: <b>{{$product->internal_sku}}</b></p><p>Barcode: <b>{{$product->barcode_value}}</b></p><p>Database ID: {{$product->id}}</p><p>Vendor: {{$product->business_name ?? '-'}}</p><p>Stock: {{$product->stock}}</p><p>Status: {{$product->status}}</p><form method="POST" action="{{route('admin.products.identifier',$product->id)}}">@csrf<button style="padding:9px 12px;border:0;border-radius:8px;background:#f59e0b">Regenerate / Ensure Identifier</button></form></section>@endif
@if($order)<section class="panel"><h2>Order Found</h2><p>Order: <b>{{$order->order_number}}</b></p><p>Status: {{$order->status}}</p><p>Payment: {{$order->payment_status}}</p><p>Total: {{$order->grand_total}} {{$order->currency}}</p><p>Created: {{$order->created_at}}</p></section>@endif
</div>
@endif
<script>
const b=document.getElementById('barcodeBtn'),s=document.getElementById('scanner'),c=document.getElementById('closeScanner');
b?.addEventListener('click',()=>{s.style.display='block';});
c?.addEventListener('click',()=>{s.style.display='none';});
</script>
@endsection