@extends('layouts.admin')
@section('title', 'Order Booster')
@section('content')
<div class="topbar">
    <h1>Order yang Ditugaskan ke Booster: {{ $booster->name }}</h1>
    <div class="top-actions">
        <a class="btn gray" href="{{ route('admin.boosters.index') }}">Kembali</a>
    </div>
</div>

<div class="toolbar">
    <form method="GET" action="{{ route('admin.boosters.show', $booster) }}">
        <div class="field">
            <label>Status</label>
            <select name="status">
                <option value="">Semua</option>
                @foreach ($statuses as $s)
                    <option value="{{ $s }}" @selected($status === $s)>{{ str_replace('_', ' ', $s) }}</option>
                @endforeach
            </select>
        </div>
        <button class="btn sm" type="submit">Filter</button>
    </form>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead><tr><th>No. Order</th><th>Pelanggan</th><th>Paket</th><th>Prioritas</th><th>Status</th><th>Dibuat</th><th>Aksi</th></tr></thead>
            <tbody>
            @forelse ($orders as $o)
                <tr>
                    <td>{{ $o->order_no }}</td>
                    <td>{{ $o->user?->name }}</td>
                    <td>{{ $o->package?->name }}</td>
                    <td>{{ $o->queue_priority }}</td>
                    <td>@include('admin.partials.status', ['status' => $o->status])</td>
                    <td>{{ $o->created_at?->format('d M Y H:i') }}</td>
                    <td><a class="btn sm" href="{{ route('admin.orders.show', $o) }}">Detail</a></td>
                </tr>
            @empty
                <tr><td colspan="7" class="empty">Belum ada order ditugaskan.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $orders->links() }}
</div>
@endsection
