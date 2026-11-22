@extends('layouts.admin')
@section('title', 'Pengguna '.$user->name)
@section('content')
<h1>{{ $user->name }} @include('admin.partials.status', ['status' => $user->status])</h1>
<div class="grid">
    <div class="stat"><small>Email</small><strong>{{ $user->email }}</strong></div>
    <div class="stat"><small>Peran</small><strong>{{ $user->role }}</strong></div>
    <div class="stat"><small>Bergabung</small><strong>{{ $user->created_at?->format('d M Y') }}</strong></div>
</div>

<div class="card">
    <h2>Aksi Akun</h2>
    @if ($user->id !== auth()->id())
        @if ($user->status === 'active')
            <form method="POST" action="{{ route('admin.users.ban', $user) }}" style="display:inline" onsubmit="return confirm('Ban {{ $user->name }}?')">
                @csrf<button class="btn red" type="submit">Ban</button>
            </form>
        @else
            <form method="POST" action="{{ route('admin.users.unban', $user) }}" style="display:inline">
                @csrf<button class="btn green" type="submit">Cabut Ban</button>
            </form>
        @endif
    @endif
    <form method="POST" action="{{ route('admin.users.reset-password', $user) }}" style="display:inline" onsubmit="return confirm('Reset password {{ $user->email }}? Password baru tampil sekali di halaman ini.')">
        @csrf<button class="btn gray" type="submit">Reset Password</button>
    </form>
</div>

<div class="card">
    <h2>Order ({{ $user->orders->count() }})</h2>
    <table>
        <thead><tr><th>No. Order</th><th>Paket</th><th>Total</th><th>Status</th></tr></thead>
        <tbody>
        @forelse ($user->orders as $o)
            <tr>
                <td><a href="{{ route('admin.orders.show', $o) }}">{{ $o->order_no }}</a></td>
                <td>{{ $o->package?->name }}</td>
                <td>Rp {{ number_format($o->total_amount, 0, ',', '.') }}</td>
                <td>@include('admin.partials.status', ['status' => $o->status])</td>
            </tr>
        @empty
            <tr><td colspan="4" class="empty">Belum ada order.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div style="margin-top:12px;"><a class="btn gray" href="{{ route('admin.users.index') }}">← Kembali</a></div>
@endsection
