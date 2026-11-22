@extends('layouts.admin')
@section('title', 'Booster')
@section('content')
<h1>Booster</h1>
<div class="card">
    <div class="table-wrap">
        <table>
            <thead><tr><th>Booster</th><th>Status</th><th>Order Aktif</th><th>Selesai</th><th>Aksi</th></tr></thead>
            <tbody>
            @forelse ($boosters as $b)
                <tr>
                    <td>{{ $b->name }}</td>
                    <td>@include('admin.partials.status', ['status' => $b->status])</td>
                    <td>{{ $b->active_orders }}</td>
                    <td>{{ $b->completed_orders }}</td>
                    <td><a class="btn sm" href="{{ route('admin.boosters.show', $b) }}">Order</a></td>
                </tr>
            @empty
                <tr><td colspan="5" class="empty">Belum ada booster.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
