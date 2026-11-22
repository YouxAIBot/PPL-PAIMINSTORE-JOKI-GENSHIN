@extends('layouts.admin')
@section('title', 'Pengguna')
@section('content')
<h1>Pengguna</h1>
<div class="toolbar">
    <form method="GET" action="{{ route('admin.users.index') }}">
        <div class="field"><label>Peran</label>
            <select name="role">
                <option value="">Semua</option>
                @foreach (['user','admin','booster'] as $r)
                    <option value="{{ $r }}" @selected($role === $r)>{{ $r }}</option>
                @endforeach
            </select>
        </div>
        <div class="field"><label>Status</label>
            <select name="status">
                <option value="">Semua</option>
                @foreach (['active','banned'] as $s)
                    <option value="{{ $s }}" @selected($status === $s)>{{ $s }}</option>
                @endforeach
            </select>
        </div>
        <div class="field"><label>Cari nama/email</label><input name="search" value="{{ request('search') }}"></div>
        <button class="btn sm" type="submit">Filter</button>
    </form>
</div>
<div class="card">
    <table>
        <thead><tr><th>Nama</th><th>Email</th><th>Peran</th><th>Status</th><th>Bergabung</th><th>Aksi</th></tr></thead>
        <tbody>
        @forelse ($users as $u)
            <tr>
                <td>{{ $u->name }}</td>
                <td>{{ $u->email }}</td>
                <td>{{ $u->role }}</td>
                <td>@include('admin.partials.status', ['status' => $u->status])</td>
                <td>{{ $u->created_at?->format('d M Y') }}</td>
                <td><a class="btn sm" href="{{ route('admin.users.show', $u) }}">Detail</a></td>
            </tr>
        @empty
            <tr><td colspan="6" class="empty">Tidak ada pengguna.</td></tr>
        @endforelse
        </tbody>
    </table>
    {{ $users->links() }}
</div>
@endsection
