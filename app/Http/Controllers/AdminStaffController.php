<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminStaffController extends Controller
{
    public function index()
    {
        $staff=DB::table('users')->whereIn('role',['admin','staff'])->latest()->paginate(25);
        $permissions=DB::table('permissions')->orderBy('name')->get();
        return view('admin.staff',compact('staff','permissions'));
    }

    public function update(Request $request,int $id)
    {
        $data=$request->validate([
            'name'=>'required|string|max:120',
            'email'=>'required|email|max:190',
            'role'=>'required|in:admin,staff',
            'status'=>'required|in:active,pending,blocked,suspended',
        ]);
        DB::table('users')->where('id',$id)->whereIn('role',['admin','staff'])->update($data);
        return back()->with('success','Staff member updated.');
    }

    public function assignPermissions(Request $request,int $id)
    {
        $data=$request->validate(['permissions'=>'nullable|array','permissions.*'=>'integer|exists:permissions,id']);
        $role=DB::table('users')->where('id',$id)->whereIn('role',['admin','staff'])->value('role');
        abort_unless($role,404);

        $roleId=DB::table('roles')->where('name',$role)->value('id');
        if(!$roleId) return back()->with('error','Role record not found.');

        DB::table('role_permissions')->where('role_id',$roleId)->delete();
        foreach($data['permissions']??[] as $permissionId){
            DB::table('role_permissions')->insert(['role_id'=>$roleId,'permission_id'=>$permissionId,'created_at'=>now(),'updated_at'=>now()]);
        }
        return back()->with('success','Permissions updated for the role.');
    }
}
