@extends('layouts.admin')
@section('title', 'Laporan')
@section('content')
<h1>Laporan Mingguan</h1>
<div class="toolbar">
    <form method="GET" action="{{ route('admin.reports.index') }}">
        <div class="field"><label>Dari</label><input type="date" name="start" value="{{ $start->toDateString() }}"></div>
        <div class="field"><label>Sampai</label><input type="date" name="end" value="{{ $end->toDateString() }}"></div>
        <button class="btn sm" type="submit">Terapkan</button>
    </form>
    <span style="flex:1"></span>
    <a class="btn sm green" href="{{ route('admin.reports.export-csv', ['start'=>$start->toDateString(), 'end'=>$end->toDateString()]) }}">Ekspor Excel (CSV)</a>
    <a class="btn sm" href="{{ route('admin.reports.export-pdf', ['start'=>$start->toDateString(), 'end'=>$end->toDateString()]) }}">Ekspor PDF</a>
</div>

<div class="grid">
    <div class="stat"><small>Pendapatan</small><strong>Rp {{ number_format($revenue, 0, ',', '.') }}</strong></div>
    <div class="stat"><small>Transaksi Sukses</small><strong>{{ number_format($success) }}</strong></div>
    <div class="stat"><small>Gagal</small><strong>{{ number_format($failed) }}</strong></div>
    <div class="stat"><small>Refund Penuh</small><strong>{{ number_format($refunded) }}</strong></div>
</div>

<div class="card">
    <h2>Tren Pendapatan per Minggu (8 minggu)</h2>
    <div class="bar-chart">
        @foreach ($trend as $t)
            <div class="bar-col">
                <div class="bar" style="height: {{ round(($t['total'] / $maxTotal) * 140) }}px" title="Rp {{ number_format($t['total'], 0, ',', '.') }}"></div>
                <div class="bar-label">{{ $t['label'] }}</div>
            </div>
        @endforeach
    </div>
</div>

<div class="card">
    <h2>Rincian per Kategori</h2>
    <table>
        <thead><tr><th>Kategori</th><th>Jumlah Order</th><th>Pendapatan</th></tr></thead>
        <tbody>
        @forelse ($perCategory as $row)
            <tr>
                <td>{{ $row['category'] }}</td>
                <td>{{ number_format($row['total_orders']) }}</td>
                <td>Rp {{ number_format((float) $row['total'], 0, ',', '.') }}</td>
            </tr>
        @empty
            <tr><td colspan="3" class="empty">Belum ada data pada periode ini.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<style>.toolbar { justify-content: flex-start; }</style>
@endsection
