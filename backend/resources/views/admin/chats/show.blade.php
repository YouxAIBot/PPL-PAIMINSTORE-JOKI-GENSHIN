@extends('layouts.admin')
@section('title', 'Chat dengan '.$conversation->user?->name)
@section('content')
<h1>Chat: {{ $conversation->user?->name }}</h1>
<div class="card">
    @foreach ($conversation->messages as $m)
        <div class="chat-msg {{ $m->sender_id === auth()->id() ? 'admin' : '' }}">
            {{ $m->body }}
            <small>{{ $m->sender?->name }} · {{ $m->created_at?->format('d M H:i') }}</small>
        </div>
    @endforeach
    @if ($conversation->messages->isEmpty())
        <p class="empty">Belum ada pesan.</p>
    @endif
</div>
<div class="card">
    <form method="POST" action="{{ route('admin.chats.reply', $conversation) }}">
        @csrf
        <label>Balas sebagai admin</label>
        <textarea name="body" rows="2" required placeholder="Tulis balasan..."></textarea>
        <button class="btn" type="submit" style="margin-top:10px">Kirim</button>
    </form>
</div>
<div style="margin-top:12px;"><a class="btn gray" href="{{ route('admin.chats.index') }}">← Kembali</a></div>
<style>.chat-msg { clear: both; }</style>
@endsection
