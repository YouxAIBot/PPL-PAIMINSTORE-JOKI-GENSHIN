@extends('layouts.admin')
@section('title', 'Kredensial')
@section('content')
<h1>Kredensial Order {{ $order->order_no }}</h1>
<div class="card">
    <table>
        <tr><th>UID</th><td>{{ $order->gameAccount->uid }}</td></tr>
        <tr><th>Server</th><td>{{ $order->gameAccount->server }}</td></tr>
        <tr><th>Login game</th><td><strong>{{ $order->gameAccount->credential_encrypted }}</strong></td></tr>
        @if ($order->gameAccount->attachment_url)
            <tr><th>Lampiran</th><td><a href="{{ $order->gameAccount->attachment_url }}" target="_blank">buka</a></td></tr>
        @endif
    </table>
    <p style="color:#6b7280;font-size:13px;">Akses ini sudah dicatat di log aktivitas.</p>
    <a class="btn gray" href="{{ route('admin.orders.show', $order) }}">← Kembali</a>
</div>
@endsection
