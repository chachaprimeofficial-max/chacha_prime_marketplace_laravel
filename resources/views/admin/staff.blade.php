@extends('admin.layout')
@section('title','Staff & Permissions — Chacha Prime')
@section('page_heading','Staff & Permissions')
@section('content')
@if(session('success'))<div class="notice">{{session('success')}}</div>@endif
@if(session('error'))<div class="notice" style="background:#fff0f0">{{session('error')}}</div>@endif
<div class="panel">
<div class="module-head"><div><h1>Staff Management</h1><p>Manage admin staff and role-level permissions.</p></div></div>
<div class="table-wrap"><table><thead><tr><th>Staff</th><th>Email</th><th>Role</th><th>Status</th><th>Actions</th></tr></thead><tbody>
@foreach($staff as $member)
<tr><td><strong>{{$member->name}}</strong><br><small>#{{$member->id}}</small></td><td>{{$member->email}}</td><td>{{$member->role}}</td><td>{{$member->status}}</td><td class="actions"><details><summary class="button button-dark">Manage</summary>
<form class="edit-form" method="POST" action="{{route('admin.staff.update',$member->id)}}">@csrf
<input name="name" value="{{$member->name}}" required><input name="email" type="email" value="{{$member->email}}" required>
<select name="role"><option value="admin" @selected($member->role==='admin')>Admin</option><option value="staff" @selected($member->role==='staff')>Staff</option></select>
<select name="status">@foreach(['active','pending','blocked','suspended'] as $st)<option value="{{$st}}" @selected($member->status===$st)>{{$st}}</option>@endforeach</select>
<button class="button button-dark">Save</button></form>
<form method="POST" action="{{route('admin.staff.permissions',$member->id)}}" class="permission-box">@csrf
<strong>Role permissions</strong><div class="permission-grid">@foreach($permissions as $permission)<label><input type="checkbox" name="permissions[]" value="{{$permission->id}}"> {{$permission->name}}</label>@endforeach</div>
<button class="button button-dark">Save Permissions</button></form>
</details></td></tr>
@endforeach
</tbody></table></div>
{{$staff->links()}}
</div>
@endsection
