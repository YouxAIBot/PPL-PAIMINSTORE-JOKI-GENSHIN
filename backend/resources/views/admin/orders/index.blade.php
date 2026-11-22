@extends('layouts.admin')
@section('title', 'Order')
@section('content')
<h1>Order</h1>
<div class="toolbar">
    <form method="GET" action="{{ route('admin.orders.index') }}">
        <div class="field"><label>Status</label>
            <select name="status">
                <option value="">Semua</option>
                @foreach ($statuses as $s)
                    <option value="{{ $s }}" @selected($status === $s)>{{ str_replace('_', ' ', $s) }}</option>
                @endforeach
            </select>
        </div>
        <div class="field"><label>Cari no. order</label><input name="search" value="{{ request('search') }}"></div>
        <button class="btn sm" type="submit">Filter</button>
    </form>
</div>
<div class="card">
    <table>
        <thead><tr><th>No. Order</th><th>Pengguna</th><th>Paket</th><th>Total</th><th>Status</th><th>Dibuat</th><th>Aksi</th></tr></thead>
        <tbody>
        @forelse ($orders as $o)
            <tr>
                <td>{{ $o->order_no }}</td>
                <td>{{ $o->user?->name }}</td>
                <td>{{ $o->package?->name }}</td>
                <td>Rp {{ number_format($o->total_amount, 0, ',', '.') }}</td>
                <td>@include('admin.partials.status', ['status' => $o->status])</td>
                <td>{{ $o->created_at?->format('d M Y H:i') }}</td>
                <td><a class="btn sm" href="{{ route('admin.orders.show', $o) }}">Detail</a></td>
            </tr>
        @empty
            <tr><td colspan="7" class="empty">Tidak ada order.</td></tr>
        @endforelse
        </tbody>
    </table>
    {{ $orders->links() }}
</div>
@endsection
