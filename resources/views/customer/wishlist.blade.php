@extends('layouts.customer')
@section('title','Wishlist — Chacha Prime')
@push('styles')
<style>
.cp-wish-head{display:flex;align-items:flex-end;justify-content:space-between;gap:16px;margin-bottom:17px}.cp-wish-head p{margin:5px 0 0;color:#64748b;font-size:12px}.cp-wish-country{background:#fff;border:1px solid #dfe6ee;border-radius:12px;padding:9px 12px;font-size:10px;color:#64748b}.cp-wish-country strong{color:#0f172a;margin-left:5px}
.cp-wish-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px}.cp-wish-card{background:#fff;border:1px solid #e2e8f0;border-radius:16px;overflow:hidden;box-shadow:0 8px 25px rgba(15,23,42,.05);display:flex;flex-direction:column}.cp-wish-image{height:180px;background:#f8fafc;display:flex;align-items:center;justify-content:center;color:#94a3b8;font-size:10px;overflow:hidden}.cp-wish-image img{width:100%;height:100%;object-fit:cover}.cp-wish-body{padding:14px;display:flex;flex-direction:column;flex:1}.cp-wish-name{font-size:13px;font-weight:800;line-height:1.4;color:#0f172a;text-decoration:none}.cp-wish-meta{font-size:10px;color:#64748b;margin-top:5px}.cp-wish-price{font-size:18px;font-weight:900;color:#0f172a;margin-top:12px}.cp-wish-stock{font-size:10px;font-weight:800;margin-top:4px}.cp-wish-stock.ok{color:#047857}.cp-wish-stock.out{color:#b91c1c}.cp-wish-actions{display:grid;grid-template-columns:1fr auto;gap:7px;margin-top:auto;padding-top:13px}.cp-wish-btn{border:1px solid #cbd5e1;background:#fff;color:#0f172a;border-radius:8px;padding:9px 10px;font-size:10px;font-weight:900;cursor:pointer}.cp-wish-btn.primary{background:#0f172a;color:#fff;border-color:#0f172a}.cp-wish-empty{background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:55px 20px;text-align:center}.cp-wish-empty h2{margin:0 0 7px;font-size:20px}.cp-wish-empty p{margin:0 0 18px;color:#64748b;font-size:12px}.cp-wish-empty a{display:inline-flex;padding:11px 15px;background:#0f172a;color:#fff;border-radius:9px;text-decoration:none;font-size:11px;font-weight:900}
@media(max-width:1100px){.cp-wish-grid{grid-template-columns:repeat(3,1fr)}}@media(max-width:800px){.cp-wish-grid{grid-template-columns:repeat(2,1fr)}}@media(max-width:520px){.cp-wish-head{display:block}.cp-wish-country{display:inline-block;margin-top:10px}.cp-wish-grid{grid-template-columns:1fr}.cp-wish-image{height:210px}}
</style>
@endpush
@section('content')
<div class="cp-wish-head"><div><h1 class="cp-customer-page-title" style="margin-bottom:0">Wishlist</h1><p>Save products you want to compare, revisit or purchase later.</p></div><div class="cp-wish-country">Marketplace <strong>{{strtoupper(optional(DB::table('countries')->where('id',$countryId)->first())->code ?? 'PK')}}</strong></div></div>
@if(session('success'))<div class="notice success" style="margin-bottom:14px">{{session('success')}}</div>@endif
@if($items->count())
<div class="cp-wish-grid">
@foreach($items as $i)
@php($product=\App\Models\Product::find($i->product_id))
<article class="cp-wish-card">
<a class="cp-wish-image" href="{{route('product',$i->product_id)}}">@if(!empty($i->image_path))<img src="{{asset('storage/'.$i->image_path)}}" alt="{{e($i->name)}}">@else<span>No image</span>@endif</a>
<div class="cp-wish-body">
<a class="cp-wish-name" href="{{route('product',$i->product_id)}}">{{\Illuminate\Support\Str::limit($i->name,65)}}</a>
<div class="cp-wish-meta">Marketplace price for selected country</div>
<div class="cp-wish-price">{{number_format((float)$i->marketplace_price,2)}} {{strtoupper($i->marketplace_currency ?? 'USD')}}</div>
<div class="cp-wish-stock {{$i->marketplace_available && $i->marketplace_stock>0?'ok':'out'}}">@if(!$i->marketplace_available)Not available in selected marketplace @elseif($i->marketplace_stock>0){{$i->marketplace_stock}} available @else Currently unavailable @endif</div>
<div class="cp-wish-actions">
@if($i->marketplace_available && $i->marketplace_stock>0)<form method="POST" action="{{route('cart.add',$i->product_id)}}">@csrf<input type="hidden" name="quantity" value="1"><button class="cp-wish-btn primary" type="submit">Move to cart</button></form>@else<a class="cp-wish-btn" href="{{route('product',$i->product_id)}}">View product</a>@endif
<form method="POST" action="{{route('customer.wishlist.remove',$i->product_id)}}">@csrf @method('DELETE')<button class="cp-wish-btn" type="submit">Remove</button></form>
</div>
</div>
</article>
@endforeach
</div>
@else
<div class="cp-wish-empty"><h2>Your wishlist is empty</h2><p>Save products from the marketplace and they will appear here.</p><a href="{{route('shop')}}">Explore marketplace</a></div>
@endif
@endsection