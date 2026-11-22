@extends('layouts.admin')
@section('title', 'Paket Joki')
@section('content')
<div class="toolbar" style="justify-content: space-between; align-items: center;">
    <h1 style="margin:0">Paket Joki</h1>
    <a class="btn" href="{{ route('admin.packages.create') }}">+ Tambah Paket</a>
</div>
<div class="card">
    <table>
        <thead><tr><th>Paket</th><th>Kategori</th><th>Harga</th><th>Estimasi</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
        @forelse ($packages as $p)
            <tr>
                <td>{{ $p->name }}</td>
                <td>{{ $p->category?->name }}</td>
                <td>Rp {{ number_format($p->price, 0, ',', '.') }}</td>
                <td>{{ $p->estimated_hours }} jam</td>
                <td>@include('admin.partials.status', ['status' => $p->is_active ? 'active' : 'banned'])</td>
                <td>
                    <a class="btn sm gray" href="{{ route('admin.packages.edit', $p) }}">Ubah</a>
                    <form method="POST" action="{{ route('admin.packages.destroy', $p) }}" style="display:inline" onsubmit="return confirm('Hapus paket ini?')">
                        @csrf @method('DELETE')
                        <button class="btn sm red" type="submit">Hapus</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="empty">Belum ada paket.</td></tr>
        @endforelse
        </tbody>
    </table>
    {{ $packages->links() }}
</div>
@endsection
