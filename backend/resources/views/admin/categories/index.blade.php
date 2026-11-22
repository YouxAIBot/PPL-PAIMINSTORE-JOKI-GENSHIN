@extends('layouts.admin')
@section('title', 'Kategori Joki')
@section('content')
<div class="toolbar" style="justify-content: space-between; align-items: center;">
    <h1 style="margin:0">Kategori Joki</h1>
    <a class="btn" href="{{ route('admin.categories.create') }}">+ Tambah Kategori</a>
</div>
<div class="card">
    <table>
        <thead><tr><th>Nama</th><th>Slug</th><th>Paket</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
        @forelse ($categories as $c)
            <tr>
                <td>{{ $c->name }}</td>
                <td>{{ $c->slug }}</td>
                <td>{{ $c->packages_count }}</td>
                <td>@include('admin.partials.status', ['status' => $c->is_active ? 'active' : 'banned'])</td>
                <td>
                    <a class="btn sm gray" href="{{ route('admin.categories.edit', $c) }}">Ubah</a>
                    <form method="POST" action="{{ route('admin.categories.destroy', $c) }}" style="display:inline" onsubmit="return confirm('Hapus kategori ini?')">
                        @csrf @method('DELETE')
                        <button class="btn sm red" type="submit">Hapus</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="5" class="empty">Belum ada kategori.</td></tr>
        @endforelse
        </tbody>
    </table>
    {{ $categories->links() }}
</div>
@endsection
