@extends('layouts.admin')
@section('title', 'Chat Pengguna')
@section('content')
<h1>Inbox Chat</h1>
<div class="card">
    <table>
        <thead><tr><th>Pengguna</th><th>Pesan Terakhir</th><th>Belum Dibaca</th><th>Aksi</th></tr></thead>
        <tbody>
        @forelse ($conversations as $c)
            @php $last = $c->messages->first(); @endphp
            <tr>
                <td>{{ $c->user?->name }}</td>
                <td>{{ $last?->body }}</td>
                <td>@if ($c->admin_unread) <span class="badge blue">{{ $c->admin_unread }}</span> @else - @endif</td>
                <td><a class="btn sm" href="{{ route('admin.chats.show', $c) }}">Buka</a></td>
            </tr>
        @empty
            <tr><td colspan="4" class="empty">Belum ada percakapan.</td></tr>
        @endforelse
        </tbody>
    </table>
    {{ $conversations->links() }}
</div>
<style>table td { max-width: 320px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }</style>
@endsection
