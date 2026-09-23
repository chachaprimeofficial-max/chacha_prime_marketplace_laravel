@extends('admin.layout')
@section('title',$title.' — Chacha Prime')
@section('page_heading',$title)
@section('content')
<div class="panel"><div class="module-head"><div><h2>{{$title}}</h2><p>Manage this marketplace module from the admin control center.</p></div></div>
@if(session('success'))<div class="notice">{{session('success')}}</div>@endif
<div class="table-wrap"><table><tr>@foreach(($rows->first() ? array_keys((array)$rows->first()) : []) as $key)<th>{{ucwords(str_replace('_',' ',$key))}}</th>@endforeach<th>Action</th></tr>
@foreach($rows as $row)<tr>@foreach((array)$row as $key=>$value)<td>{{is_array($value)?json_encode($value):Str::limit((string)$value,80)}}</td>@endforeach<td>@if(in_array($module,['brands','reviews','coupons','shipping','currencies','payment-methods','pages','group-buying','live-commerce']))<form method="POST" action="{{route('admin.module.toggle',[$module,$row->id])}}">@csrf<button class="button button-dark">Toggle</button></form>@else—@endif</td></tr>@endforeach</table></div>{{$rows->links()}}</div>
@endsection