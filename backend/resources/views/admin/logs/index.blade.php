@extends('layouts.admin')
@section('title', 'Log Aktivitas')
@section('content')
<h1>Log Aktivitas Admin</h1>
<div class="toolbar">
    <form method="GET" action="{{ route('admin.logs.index') }}">
        <div class="field"><label>Aksi</label><input name="action" value="{{ $action }}" placeholder="mis. order."></div>
        <button class="btn sm" type="submit">Filter</button>
    </form>
</div>
<div class="card">
    <table>
        <thead><tr><th>Waktu</th><th>Aktor</th><th>Aksi</th><th>Entitas</th><th>IP</th><th>Meta</th></tr></thead>
        <tbody>
        @forelse ($logs as $l)
            <tr>
                <td>{{ $l->created_at?->format('d M Y H:i:s') }}</td>
                <td>{{ $l->actor?->name ?? 'sistem' }}</td>
                <td><code>{{ $l->action }}</code></td>
                <td>{{ $l->entity_type }} {{ $l->entity_id ? '#'.$l->entity_id : '' }}</td>
                <td>{{ $l->ip }}</td>
                <td><small>{{ json_encode($l->meta) }}</small></td>
            </tr>
        @empty
            <tr><td colspan="6" class="empty">Belum ada log.</td></tr>
        @endforelse
        </tbody>
    </table>
    {{ $logs->links() }}
</div>
<style>table td { max-width: 220px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }</style>
@endsection
