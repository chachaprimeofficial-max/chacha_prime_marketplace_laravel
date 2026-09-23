@extends('admin.layout')
@section('title','Categories — Chacha Prime')
@section('page_heading','Category Management')
@section('content')
<div class="panel"><h2>Categories</h2>@if(session('success'))<p style="color:#17652a">{{ session('success') }}</p>@endif
<form method="POST" action="{{ route('admin.categories.store') }}" style="display:grid;grid-template-columns:1fr 1fr 2fr;gap:12px;margin-bottom:25px">@csrf<input name="name" placeholder="Category name" required><input name="parent_id" placeholder="Parent ID"><input name="description" placeholder="Description"><button class="button button-dark" type="submit">Create Category</button></form>
<table style="width:100%;border-collapse:collapse"><tr><th align="left">Name</th><th>Slug</th><th>Status</th></tr>@foreach($categories as $category)<tr><td style="padding:12px 4px">{{ $category->name }}</td><td>{{ $category->slug }}</td><td>{{ $category->status }}</td></tr>@endforeach</table>{{ $categories->links() }}</div>
@endsection