@extends('layouts.admin')
@section('title', 'Leaderboard')
@section('content')
<h1>Leaderboard</h1>
<div class="toolbar">
    <form method="POST" action="{{ route('admin.leaderboard.store') }}">
        @csrf
        <div class="field"><label>Periode</label>
            <select name="period">
                <option value="all" @selected($period === 'all')>Semua waktu</option>
                <option value="month" @selected($period === 'month')>Bulan ini</option>
                <option value="week" @selected($period === 'week')>Minggu ini</option>
            </select>
        </div>
        <button class="btn sm" type="submit">Simpan periode</button>
    </form>
    <form method="POST" action="{{ route('admin.leaderboard.store') }}">
        @csrf<input type="hidden" name="action" value="reset">
        <button class="btn sm red" type="submit" onclick="return confirm('Reset leaderboard ke semua waktu?')">Reset</button>
    </form>
</div>
<div class="card">
    <table>
        <thead><tr><th>Rank</th><th>Nama</th><th>Total Belanja</th></tr></thead>
        <tbody>
        @foreach ($rows as $i => $r)
            <tr>
                <td>#{{ $i + 1 }}</td>
                <td>{{ $r['name'] }}</td>
                <td>Rp {{ number_format($r['total'], 0, ',', '.') }}</td>
            </tr>
        @endforeach
        @if ($rows->isEmpty())
            <tr><td colspan="3" class="empty">Belum ada order selesai.</td></tr>
        @endif
        </tbody>
    </table>
</div>
<style>form { display: inline-block; }</style>
@endsection
