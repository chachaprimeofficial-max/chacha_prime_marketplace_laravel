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
@elseif($module==='coupons')<input name="code" placeholder="Coupon code" required><input name="type" placeholder="fixed/percent" required><input name="value" type="number" step="0.01" placeholder="Value" required><input name="min_order" type="number" step="0.01" placeholder="Minimum order"><input name="max_discount" type="number" step="0.01" placeholder="Maximum discount">
@elseif($module==='currencies')<input name="code" placeholder="USD" required><input name="name" placeholder="Currency name" required><input name="symbol" placeholder="$"><input name="rate_to_base" type="number" step="0.0000000001" placeholder="Rate" required>
@elseif($module==='payment-methods')<input name="code" placeholder="gateway_code" required><input name="name" placeholder="Payment method name" required><input name="type" placeholder="gateway/bank/cod" required>
@elseif($module==='shipping')<input name="code" placeholder="shipping_code" required><input name="name" placeholder="Shipping method" required><input name="provider" placeholder="Provider"><input name="mode" placeholder="api/manual" required>
@elseif($module==='pages')<input name="slug" placeholder="about-us" required><input name="title" placeholder="Page title" required><textarea name="content" placeholder="Page content"></textarea>@endif
<button class="button button-dark">Create</button></form>
@endif
<div class="table-wrap"><table><tr>@foreach(($rows->first() ? array_keys((array)$rows->first()) : []) as $key)<th>{{ucwords(str_replace('_',' ',$key))}}</th>@endforeach<th>Actions</th></tr>
@foreach($rows as $row)<tr>@foreach((array)$row as $key=>$value)<td>{{is_array($value)?json_encode($value):Str::limit((string)$value,70)}}</td>@endforeach<td class="actions">
@if(in_array($module,['brands','coupons','shipping','currencies','payment-methods','pages']))<details><summary>Edit</summary><form method="POST" action="{{route('admin.module.update',[$module,$row->id])}}" class="edit-form">@csrf
@if($module==='brands')<input name="name" value="{{data_get($row,'name')}}" required><input name="logo" value="{{data_get($row,'logo')}}">
@elseif($module==='coupons')<input name="code" value="{{data_get($row,'code')}}" required><input name="type" value="{{data_get($row,'type')}}" required><input name="value" type="number" step="0.01" value="{{data_get($row,'value')}}" required><input name="min_order" type="number" step="0.01" value="{{data_get($row,'min_order')}}"><input name="max_discount" type="number" step="0.01" value="{{data_get($row,'max_discount')}}">
@elseif($module==='currencies')<input name="name" value="{{data_get($row,'name')}}" required><input name="symbol" value="{{data_get($row,'symbol')}}"><input name="rate_to_base" type="number" step="0.0000000001" value="{{data_get($row,'rate_to_base')}}" required>
@elseif($module==='payment-methods')<input name="name" value="{{data_get($row,'name')}}" required><input name="type" value="{{data_get($row,'type')}}" required>
@elseif($module==='shipping')<input name="name" value="{{data_get($row,'name')}}" required><input name="provider" value="{{data_get($row,'provider')}}"><input name="mode" value="{{data_get($row,'mode')}}" required>
@elseif($module==='pages')<input name="slug" value="{{data_get($row,'slug')}}" required><input name="title" value="{{data_get($row,'title')}}" required><textarea name="content">{{data_get($row,'content')}}</textarea>@endif
<button class="button button-dark">Save</button></form></details>
<form method="POST" action="{{route('admin.module.delete',[$module,$row->id])}}" style="display:inline">@csrf @method('DELETE')<button class="button button-danger" onclick="return confirm('Delete this record?')">Delete</button></form>
@endif
@if(in_array($module,['brands','reviews','coupons','shipping','currencies','payment-methods','pages','group-buying','live-commerce']))<form method="POST" action="{{route('admin.module.toggle',[$module,$row->id])}}" style="display:inline">@csrf<button class="button button-dark">Toggle</button></form>@endif
</td></tr>@endforeach</table></div>{{$rows->links()}}</div>
@endsection