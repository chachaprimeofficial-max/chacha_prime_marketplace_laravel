@extends('admin.layout')
@section('title',$title.' — Chacha Prime')
@section('page_heading',$title)
@section('content')
<div class="panel">
@if(session('success'))<div class="notice">{{session('success')}}</div>@endif
<h2>{{$title}}</h2>
@if(in_array($module,['brands','coupons','currencies','payment-methods','shipping','pages']))
<form method="POST" action="{{route('admin.module.store',$module)}}" class="module-form">@csrf
@if($module==='brands')<input name="name" placeholder="Brand name" required><input name="logo" placeholder="Logo URL">
@elseif($module==='coupons')<input name="code" placeholder="Coupon code" required><input name="type" placeholder="fixed or percent" required><input name="value" type="number" step="0.01" placeholder="Value" required><input name="min_order" type="number" step="0.01" placeholder="Minimum order"><input name="max_discount" type="number" step="0.01" placeholder="Maximum discount">
@elseif($module==='currencies')<input name="code" placeholder="USD" required><input name="name" placeholder="Currency name" required><input name="symbol" placeholder="$"><input name="rate_to_base" type="number" step="0.0000000001" placeholder="Rate" required>
@elseif($module==='payment-methods')<input name="code" placeholder="gateway_code" required><input name="name" placeholder="Payment method name" required><input name="type" placeholder="gateway/bank/cod" required>
@elseif($module==='shipping')<input name="code" placeholder="shipping_code" required><input name="name" placeholder="Shipping method" required><input name="provider" placeholder="Provider"><input name="mode" placeholder="api/manual" required>
@elseif($module==='pages')<input name="slug" placeholder="about-us" required><input name="title" placeholder="Page title" required><textarea name="content" placeholder="Page content"></textarea>@endif
<button class="button button-dark">Create</button></form>
@endif
<div class="table-wrap"><table><tr>@foreach(($rows->first() ? array_keys((array)$rows->first()) : []) as $key)<th>{{ucwords(str_replace('_',' ',$key))}}</th>@endforeach<th>Action</th></tr>
@foreach($rows as $row)<tr>@foreach((array)$row as $key=>$value)<td>{{is_array($value)?json_encode($value):Str::limit((string)$value,80)}}</td>@endforeach<td>@if(in_array($module,['brands','reviews','coupons','shipping','currencies','payment-methods','pages','group-buying','live-commerce']))<form method="POST" action="{{route('admin.module.toggle',[$module,$row->id])}}">@csrf<button class="button button-dark">Toggle</button></form>@else—@endif</td></tr>@endforeach</table></div>{{$rows->links()}}</div>
@endsection