@extends('layouts.customer')
@section('title','Choose Marketplace Country')
@section('content')
<div style="max-width:1000px;margin:30px auto;padding:20px">
 <div style="background:#fff;border:1px solid #e5e7eb;border-radius:18px;padding:24px">
  <div style="font-size:11px;font-weight:900;letter-spacing:1.4px;color:#d97706">CHACHA PRIME / MARKETPLACE</div>
  <h1 style="margin:6px 0;font-size:30px">Choose your shopping country</h1>
  <p style="color:#64748b">Your selected country controls the marketplace catalog, delivery destination and country-specific listings.</p>
  <form method="POST" action="{{route('marketplace.country.select')}}" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(210px,1fr));gap:10px;margin-top:20px">@csrf
   @foreach($countries as $country)
    <button name="country_id" value="{{$country->id}}" type="submit" style="text-align:left;padding:15px;border:1px solid {{(int)$selected===(int)$country->id?'#f59e0b':'#e5e7eb'}};background:{{(int)$selected===(int)$country->id?'#fffbeb':'#fff'}};border-radius:12px;cursor:pointer">
     <strong style="font-size:14px"><span style="display:inline-flex;min-width:28px;height:20px;align-items:center;justify-content:center;border:1px solid #cbd5e1;border-radius:5px;font-size:8px;font-weight:900;margin-right:6px">{{$country->code}}</span>{{$country->name}}</strong><br><small style="color:#64748b">{{$country->code}} · {{$country->currency_code}}</small>
    </button>
   @endforeach
  </form>
 </div>
</div>
@endsection
