@extends('layouts.admin')
@section('title', 'Pembayaran')
@section('content')
<h1>Pembayaran</h1>
<div class="toolbar">
    <form method="GET" action="{{ route('admin.payments.index') }}">
        <div class="field"><label>Metode</label>
            <select name="method">
                <option value="">Semua</option>
                <option value="manual" @selected($method === 'manual')>manual</option>
                <option value="gateway" @selected($method === 'gateway')>gateway</option>
            </select>
        </div>
        <div class="field"><label>Status</label>
            <select name="status">
                <option value="">Semua</option>
                @foreach (['pending','paid','failed','refunded'] as $s)
                    <option value="{{ $s }}" @selected($status === $s)>{{ $s }}</option>
                @endforeach
            </select>
        </div>
        <button class="btn sm" type="submit">Filter</button>
    </form>
</div>
<div class="card">
    <table>
        <thead><tr><th>ID</th><th>Order</th><th>Metode</th><th>Jumlah</th><th>Refunded</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
        @forelse ($payments as $p)
            <tr>
                <td>#{{ $p->id }}</td>
                <td><a href="{{ route('admin.orders.show', $p->order) }}">{{ $p->order?->order_no }}</a></td>
                <td>{{ $p->method }}</td>
                <td>Rp {{ number_format($p->amount, 0, ',', '.') }}</td>
                <td>Rp {{ number_format($p->refunded_amount, 0, ',', '.') }}</td>
                <td>@include('admin.partials.status', ['status' => $p->status])</td>
                <td>
                    @if ($p->status === 'pending' && $p->method === 'manual')
                        <form method="POST" action="{{ route('admin.payments.approve', $p) }}" style="display:inline">
                            @csrf<button class="btn sm green" type="submit">Setujui</button>
                        </form>
                        <form method="POST" action="{{ route('admin.payments.reject', $p) }}" style="display:inline" onsubmit="return confirm('Tolak bukti transfer ini?')">
                            @csrf<input type="hidden" name="reason" value="Bukti transfer ditolak admin">
                            <button class="btn sm red" type="submit">Tolak</button>
                        </form>
                    @endif
                    @if (in_array($p->status, ['paid','refunded'], true) && ($p->amount - $p->refunded_amount) > 0)
                        <form method="POST" action="{{ route('admin.payments.refund', $p) }}" style="display:flex;gap:6px;align-items:center">
                            @csrf
                            <input type="number" name="amount" min="1" max="{{ $p->amount - $p->refunded_amount }}" placeholder="Rp" style="width:110px;padding:5px">
                            <button class="btn sm gray" type="submit">Refund</button>
                        </form>
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="7" class="empty">Tidak ada pembayaran.</td></tr>
        @endforelse
        </tbody>
    </table>
    {{ $payments->links() }}
</div>
<style>table form { margin-top: 0; }</style>
@endsection
