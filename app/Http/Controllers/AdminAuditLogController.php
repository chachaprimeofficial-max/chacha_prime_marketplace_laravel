<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminAuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('audit_logs')
            ->leftJoin('users', 'users.id', '=', 'audit_logs.user_id')
            ->select(
                'audit_logs.*',
                'users.name as actor_name',
                'users.email as actor_email'
            )
            ->orderByDesc('audit_logs.id');

        if ($request->filled('action')) {
            $query->where('audit_logs.action', 'like', '%'.$request->string('action')->toString().'%');
        }

        if ($request->filled('entity_type')) {
            $query->where('audit_logs.entity_type', $request->string('entity_type')->toString());
        }

        if ($request->filled('user_id')) {
            $query->where('audit_logs.user_id', (int) $request->input('user_id'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('audit_logs.created_at', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('audit_logs.created_at', '<=', $request->input('date_to'));
        }

        $logs = $query->paginate(40)->withQueryString();

        $actors = DB::table('users')
            ->whereIn('role', ['super_admin', 'admin'])
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        $entityTypes = DB::table('audit_logs')
            ->whereNotNull('entity_type')
            ->select('entity_type')
            ->distinct()
            ->orderBy('entity_type')
            ->pluck('entity_type');

        return view('admin.audit-logs', compact('logs', 'actors', 'entityTypes'));
    }
}
