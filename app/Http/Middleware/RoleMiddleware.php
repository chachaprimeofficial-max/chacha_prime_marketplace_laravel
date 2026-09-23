<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user=$request->user();
        if(!$user || !in_array($user->role,$roles,true)) abort(403);

        if(in_array($user->role,['staff','admin'],true) && !$this->allowed($user->role,$request)){
            abort(403,'You do not have permission for this action.');
        }

        return $next($request);
    }

    protected function allowed(string $role, Request $request): bool
    {
        if(in_array($role,['admin','super_admin'],true)) return true;
        $route=$request->route()?->getName() ?? '';
        $permissionMap=[
            'admin.dashboard'=>'dashboard.view','admin.users'=>'users.view','admin.users.update'=>'users.manage',
            'admin.vendors'=>'vendors.manage','admin.products'=>'products.manage','admin.products.store'=>'products.manage',
            'admin.categories'=>'categories.manage','admin.categories.store'=>'categories.manage',
            'admin.orders'=>'orders.view','admin.orders.update'=>'orders.manage','admin.payments'=>'payments.view',
            'admin.payments.update'=>'payments.manage','admin.wallets'=>'wallets.manage','admin.wallets.adjust'=>'wallets.manage',
            'admin.cards'=>'cards.manage','admin.cards.issue'=>'cards.manage','admin.staff'=>'staff.manage',
            'admin.staff.update'=>'staff.manage','admin.staff.permissions'=>'permissions.manage',
   'admin.audit-logs'=>'audit.view',
        ];
        $permission=$permissionMap[$route] ?? (str_starts_with($route,'admin.module')?'modules.manage':null);
        if(!$permission) return false;
        $roleId=DB::table('roles')->where('name',$role)->value('id');
        $permissionId=DB::table('permissions')->where('name',$permission)->value('id');
        return $roleId && $permissionId && DB::table('role_permissions')->where('role_id',$roleId)->where('permission_id',$permissionId)->exists();
    }
}
