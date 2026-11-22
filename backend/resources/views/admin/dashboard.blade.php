@extends('layouts.admin')
@section('title', 'Dashboard')
@section('content')
<div class="topbar">
    <div>
        <h1>Panel Admin</h1>
        <span class="role-badge">Role: {{ $roleLabel }}</span>
    </div>
    <div class="top-actions">
        <a class="btn outline" href="{{ route('admin.reports.export-csv') }}">Export Excel</a>
        <a class="btn" href="{{ route('admin.orders.create') }}">Tambah Order</a>
    </div>
</div>

<div class="grid">
    <div class="stat"><small>Total Order Hari Ini</small><strong>{{ number_format($todayOrders) }}</strong></div>
    <div class="stat"><small>Pendapatan Hari Ini</small><strong>Rp {{ number_format($todayRevenue, 0, ',', '.') }}</strong></div>
    <div class="stat"><small>Antrian</small><strong>{{ number_format($queueCount) }}</strong></div>
    <div class="stat"><small>Perlu Verifikasi</small><strong>{{ number_format($needVerification) }}</strong></div>
</div>

<div class="card">
    <h2>Daftar Antrian Aktif</h2>
    <div class="table-wrap">
        <table>
            <thead><tr><th>No. Order</th><th>Pengguna</th><th>Paket</th><th>Prioritas</th><th>Status</th><th>Aksi</th></tr></thead>
            <tbody>
            @forelse ($activeQueue as $o)
                <tr>
                    <td><a href="{{ route('admin.orders.show', $o) }}">{{ $o->order_no }}</a></td>
                    <td>{{ $o->user?->name }}</td>
                    <td>{{ $o->package?->name }}</td>
                    <td>{{ $o->queue_priority }}</td>
                    <td>@include('admin.partials.status', ['status' => $o->status])</td>
                    <td><a class="btn sm" href="{{ route('admin.orders.show', $o) }}">Kelola</a></td>
                </tr>
            @empty
                <tr><td colspan="6" class="empty">Tidak ada antrian aktif.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <h2>Verifikasi Pembayaran &amp; Penugasan Booster</h2>
    <div class="table-wrap">
        <table>
            <thead><tr><th>No. Order</th><th>Pengguna</th><th>Paket</th><th>Metode</th><th>Jumlah</th><th>Aksi</th></tr></thead>
            <tbody>
            @forelse ($verifications as $p)
                <tr>
                    <td>{{ $p->order?->order_no }}</td>
                    <td>{{ $p->order?->user?->name }}</td>
                    <td>{{ $p->order?->package?->name }}</td>
                    <td>{{ $p->method }}</td>
                    <td>Rp {{ number_format($p->amount, 0, ',', '.') }}</td>
                    <td>
                        <form method="POST" action="{{ route('admin.payments.approve', $p) }}" style="display:inline">
                            @csrf
                            <button class="btn sm green" type="submit">Verifikasi</button>
                        </form>
                        <a class="btn sm gray" href="{{ route('admin.orders.show', $p->order) }}">Detail</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="empty">Tidak ada pembayaran perlu diverifikasi.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
