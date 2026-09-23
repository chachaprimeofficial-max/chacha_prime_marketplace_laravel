@extends('admin.layout')

@section('title','Audit Logs — Chacha Prime')
@section('page_heading','Audit Log')

@section('content')
<div class="panel">
    <div class="module-head">
        <div>
            <h2 style="margin:0">Audit Log</h2>
            <p style="color:#707782;margin:6px 0 0">Track administrative actions without storing passwords, secrets or card tokens.</p>
        </div>
    </div>

    <form method="GET" action="{{ route('admin.audit-logs') }}" class="module-form">
        <input name="action" value="{{ request('action') }}" placeholder="Action e.g. user.updated">
        <select name="entity_type">
            <option value="">All entities</option>
            @foreach($entityTypes as $type)
                <option value="{{ $type }}" @selected(request('entity_type') === $type)>{{ $type }}</option>
            @endforeach
        </select>
        <select name="user_id">
            <option value="">All actors</option>
            @foreach($actors as $actor)
                <option value="{{ $actor->id }}" @selected((string)request('user_id') === (string)$actor->id)>{{ $actor->name }} — {{ $actor->email }}</option>
            @endforeach
        </select>
        <input type="date" name="date_from" value="{{ request('date_from') }}">
        <input type="date" name="date_to" value="{{ request('date_to') }}">
        <button class="button button-dark" type="submit">Filter</button>
        <a class="button" href="{{ route('admin.audit-logs') }}">Reset</a>
    </form>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Time</th>
                    <th>Actor</th>
                    <th>Action</th>
                    <th>Entity</th>
                    <th>IP</th>
                    <th>Details</th>
                </tr>
            </thead>
            <tbody>
            @forelse($logs as $log)
                <tr>
                    <td>{{ $log->created_at }}</td>
                    <td>
                        <strong>{{ $log->actor_name ?: 'System' }}</strong>
                        @if($log->actor_email)<br><small>{{ $log->actor_email }}</small>@endif
                    </td>
                    <td>{{ $log->action }}</td>
                    <td>{{ $log->entity_type ?: '—' }}{{ $log->entity_id ? ' #'.$log->entity_id : '' }}</td>
                    <td>{{ $log->ip_address ?: '—' }}</td>
                    <td>
                        <details>
                            <summary>View</summary>
                            <pre style="white-space:pre-wrap;max-width:520px">{{ json_encode(json_decode($log->metadata ?: '{}', true), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</pre>
                        </details>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6">No audit records found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:16px">{{ $logs->links() }}</div>
</div>
@endsection
