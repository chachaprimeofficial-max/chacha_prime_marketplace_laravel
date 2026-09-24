@extends('admin.layout')
@section('title','Staff & Permissions — Chacha Prime')
@section('page_heading','Staff & Permissions')
@section('content')
@if(session('success'))<div class="notice">{{session('success')}}</div>@endif
@if(session('error'))<div class="notice" style="background:#fff0f0">{{session('error')}}</div>@endif
<style>.cp-admin-page{background:#f6f8fb;border:1px solid #e2e8f0;border-radius:18px;padding:20px}.cp-admin-page h2,.cp-admin-page h1{color:#0f172a}.cp-admin-page table{width:100%;border-collapse:separate;border-spacing:0}.cp-admin-page th{background:#f8fafc;color:#64748b;font-size:10px;text-transform:uppercase;letter-spacing:.6px;padding:13px 10px;text-align:left}.cp-admin-page td{padding:12px 10px;border-top:1px solid #edf2f7;font-size:12px;color:#334155}.cp-admin-page input,.cp-admin-page select,.cp-admin-page textarea{border:1px solid #cfd8e3;border-radius:9px;padding:9px 10px;background:#fff;font-size:12px}.cp-admin-page button,.cp-admin-page .button{border:0;border-radius:9px;padding:9px 13px;background:#0f172a;color:#fff;font-size:11px;font-weight:800}.cp-admin-form{display:flex;gap:10px;flex-wrap:wrap;padding:15px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:14px;margin-bottom:16px}.cp-admin-form>*{flex:1;min-width:150px}@media(max-width:650px){.cp-admin-page{padding:14px;overflow:auto}.cp-admin-page table{min-width:800px}.cp-admin-form{display:grid;grid-template-columns:1fr}.cp-admin-form>*{min-width:0}}</style><div class="cp-admin-page">
<div class="module-head"><div><h1>Staff Management</h1><p>Manage admin staff and role-level permissions. Only the Super Admin can assign or change permissions.</p></div></div>
<div class="table-wrap"><table><thead><tr><th>Staff</th><th>Email</th><th>Role</th><th>Status</th><th>Actions</th></tr></thead><tbody>
@foreach($staff as $member)
<tr><td><strong>{{$member->name}}</strong><br><small>#{{$member->id}}</small></td><td>{{$member->email}}</td><td>{{$member->role}}</td><td>{{$member->status}}</td><td class="actions"><details><summary class="button button-dark">Manage</summary>
<form class="edit-form" method="POST" action="{{route('admin.staff.update',$member->id)}}">@csrf
<input name="name" value="{{$member->name}}" required><input name="email" type="email" value="{{$member->email}}" required>
<select name="role"><option value="admin" @selected($member->role==='admin')>Admin</option><option value="staff" @selected($member->role==='staff')>Staff</option></select>
<select name="status">@foreach(['active','pending','blocked','suspended'] as $st)<option value="{{$st}}" @selected($member->status===$st)>{{$st}}</option>@endforeach</select>
<button class="button button-dark">Save</button></form>
<form method="POST" action="{{route('admin.staff.permissions',$member->id)}}" class="permission-box">@csrf
<strong>Role permissions</strong><div class="permission-grid">@foreach($permissions as $permission)<label><input type="checkbox" name="permissions[]" value="{{$permission->id}}" @checked(in_array($permission->id,$rolePermissionMap[$member->role]??[]))> {{$permission->name}}</label>@endforeach</div>
<button class="button button-dark">Save Permissions</button></form>
</details></td></tr>
@endforeach
</tbody></table></div>
{{$staff->links()}}
</div>
@endsection
