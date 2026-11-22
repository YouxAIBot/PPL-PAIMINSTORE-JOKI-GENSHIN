@extends('layouts.admin')
@section('title', 'Tambah Order')
@section('content')
<div class="topbar">
    <h1>Tambah Order</h1>
    <div class="top-actions">
        <a class="btn gray" href="{{ route('admin.orders.index') }}">Kembali</a>
    </div>
</div>

<div class="card">
    <form method="POST" action="{{ route('admin.orders.store') }}">
        @csrf
        <div class="row">
            <div>
                <label>Pelanggan</label>
                <select name="user_id" required>
                    <option value="">Pilih pelanggan</option>
                    @foreach ($customers as $c)
                        <option value="{{ $c->id }}" @selected(old('user_id') == $c->id)>{{ $c->name }} ({{ $c->email }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label>Paket</label>
                <select name="package_id" required>
                    <option value="">Pilih paket</option>
                    @foreach ($packages as $p)
                        <option value="{{ $p->id }}" @selected(old('package_id') == $p->id)>
                            {{ $p->name }} — {{ $p->category?->name }} — Rp {{ number_format($p->price, 0, ',', '.') }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="row">
            <div>
                <label>UID Game</label>
                <input name="uid" value="{{ old('uid') }}" required>
            </div>
            <div>
                <label>Server</label>
                <select name="server" required>
                    @foreach (['asia', 'europe', 'america', 'twhkmo'] as $server)
                        <option value="{{ $server }}" @selected(old('server') === $server)>{{ strtoupper($server) }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <label>Kredensial Akun Game</label>
        <textarea name="credential" rows="3" required placeholder="Email / UID / password pelanggan">{{ old('credential') }}</textarea>

        <div class="row">
            <div>
                <label>Booster (opsional)</label>
                <select name="booster_id">
                    <option value="">Belum ditugaskan</option>
                    @foreach ($boosters as $b)
                        <option value="{{ $b->id }}" @selected(old('booster_id') == $b->id)>{{ $b->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label>Status Awal</label>
                <select name="status" required>
                    <option value="pending_payment" @selected(old('status') === 'pending_payment')>Menunggu Pembayaran</option>
                    <option value="queued" @selected(old('status') === 'queued')>Antrian</option>
                </select>
            </div>
            <div>
                <label>Prioritas Antrian</label>
                <input name="queue_priority" type="number" value="{{ old('queue_priority', 0) }}">
            </div>
        </div>

        <label>Catatan Pelanggan</label>
        <textarea name="customer_notes" rows="2">{{ old('customer_notes') }}</textarea>

        <div style="margin-top:16px">
            <button class="btn green" type="submit">Simpan Order</button>
        </div>
    </form>
</div>
@endsection
