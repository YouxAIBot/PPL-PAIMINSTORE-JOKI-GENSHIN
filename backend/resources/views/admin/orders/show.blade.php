@extends('layouts.admin')
@section('title', 'Order '.$order->order_no)
@section('content')
<h1>Order {{ $order->order_no }} @include('admin.partials.status', ['status' => $order->status])</h1>

<div class="grid">
    <div class="stat"><small>Pengguna</small><strong>{{ $order->user?->name }}</strong></div>
    <div class="stat"><small>Paket</small><strong>{{ $order->package?->name }}</strong></div>
    <div class="stat"><small>Booster</small><strong>{{ $order->booster?->name ?? '-' }}</strong></div>
    <div class="stat"><small>Prioritas Antrian</small><strong>{{ $order->queue_priority }}</strong></div>
    <div class="stat"><small>Total</small><strong>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</strong></div>
    <div class="stat"><small>Dibuat</small><strong>{{ $order->created_at?->format('d M Y H:i') }}</strong></div>
</div>

@if ($order->customer_notes)
<div class="card"><h2>Catatan Pelanggan</h2><p>{{ $order->customer_notes }}</p></div>
@endif

@if ($order->cancel_reason)
<div class="card"><h2>Alasan Pembatalan</h2><p>{{ $order->cancel_reason }}</p></div>
@endif

@if ($order->payment)
<div class="card">
    <h2>Pembayaran</h2>
    <table>
        <tr><th>Metode</th><td>{{ $order->payment->method }}</td></tr>
        <tr><th>Jumlah</th><td>Rp {{ number_format($order->payment->amount, 0, ',', '.') }}</td></tr>
        <tr><th>Refunded</th><td>Rp {{ number_format($order->payment->refunded_amount, 0, ',', '.') }}</td></tr>
        <tr><th>Status</th><td>@include('admin.partials.status', ['status' => $order->payment->status])</td></tr>
        <tr><th>Diverifikasi oleh</th><td>{{ $order->payment->verifier?->name ?? '-' }}</td></tr>
        <tr><th>Bukti</th><td><a href="{{ $order->payment->proof_url }}" target="_blank">{{ $order->payment->proof_url ?? '-' }}</a></td></tr>
    </table>
</div>
@endif

<div class="card">
    <h2>Kredensial Akun Game</h2>
    <form method="POST" action="{{ route('admin.orders.credentials', $order) }}">
        @csrf
        <button class="btn green" type="submit">Tampilkan kredensial</button>
    </form>
    <p style="color:#6b7280;font-size:13px;">Setiap akses dicatat di log aktivitas.</p>
</div>

<div class="row">
    <div class="card">
        <h2>Tugaskan Booster</h2>
        <form method="POST" action="{{ route('admin.orders.assign', $order) }}">
            @csrf
            <label>Booster aktif</label>
            <select name="booster_id" required>
                <option value="">— pilih —</option>
                @foreach ($boosters as $b)
                    <option value="{{ $b->id }}" @selected($order->booster_id == $b->id)>{{ $b->name }}</option>
                @endforeach
            </select>
            <button class="btn sm" type="submit" style="margin-top:10px">Simpan</button>
        </form>
    </div>

    @if (in_array($order->status, ['queued', 'in_progress'], true))
    <div class="card">
        <h2>Ubah Status</h2>
        <form method="POST" action="{{ route('admin.orders.status', $order) }}">
            @csrf
            <label>Status berikutnya</label>
            <select name="status" required>
                @if ($order->status === 'queued')
                    <option value="in_progress">in_progress</option>
                @elseif ($order->status === 'in_progress')
                    <option value="completed">completed</option>
                @endif
            </select>
            <button class="btn sm" type="submit" style="margin-top:10px">Simpan</button>
        </form>
    </div>
    @endif

    @if ($order->status === 'queued')
    <div class="card">
        <h2>Prioritas Antrian</h2>
        <form method="POST" action="{{ route('admin.orders.priority', $order) }}">
            @csrf
            <label>Nilai prioritas (makin besar makin didahulukan)</label>
            <input type="number" name="queue_priority" value="{{ $order->queue_priority }}" min="-999" max="999" required>
            <button class="btn sm" type="submit" style="margin-top:10px">Simpan</button>
        </form>
    </div>
    @endif

    @if ($order->isCancellable())
    <div class="card">
        <h2>Batalkan Order</h2>
        <form method="POST" action="{{ route('admin.orders.cancel', $order) }}">
            @csrf
            <label>Alasan pembatalan</label>
            <textarea name="reason" rows="2" required></textarea>
            <button class="btn sm red" type="submit" style="margin-top:10px">Batalkan</button>
        </form>
    </div>
    @endif
</div>

<div class="card">
    <h2>Riwayat</h2>
    <table>
        <thead><tr><th>Waktu</th><th>Aktor</th><th>Aksi</th><th>Meta</th></tr></thead>
        <tbody>
        @forelse ($logs as $log)
            <tr>
                <td>{{ $log->created_at?->format('d M Y H:i:s') }}</td>
                <td>{{ $log->actor?->name ?? ' sistem' }}</td>
                <td><code>{{ $log->action }}</code></td>
                <td>{{ json_encode($log->meta) }}</td>
            </tr>
        @empty
            <tr><td colspan="4" class="empty">Belum ada riwayat.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div style="margin-top:12px;"><a class="btn gray" href="{{ route('admin.orders.index') }}">← Kembali</a></div>
@endsection
