<!doctype html>
<html lang="id">
<head><meta charset="utf-8"><style>
    body { font-family: sans-serif; color: #111; }
    h1 { font-size: 18px; margin-bottom: 4px; }
    p.sub { color: #555; font-size: 12px; margin-top: 0; }
    table { width: 100%; border-collapse: collapse; font-size: 12px; margin-top: 12px; }
    th, td { border: 1px solid #999; padding: 6px 8px; text-align: left; }
    .stats { display: flex; gap: 10px; margin-top: 12px; }
    .stat { border: 1px solid #999; padding: 8px 12px; border-radius: 6px; }
    .stat strong { display: block; font-size: 15px; }
</style></head>
<body>
<h1>Laporan Paiminstore Joki Genshin</h1>
<p class="sub">Periode {{ $start->toDateString() }} s.d. {{ $end->toDateString() }}</p>
<div class="stats">
    <div class="stat"><small>Pendapatan</small><strong>Rp {{ number_format($revenue, 0, ',', '.') }}</strong></div>
    <div class="stat"><small>Sukses</small><strong>{{ number_format($success) }}</strong></div>
    <div class="stat"><small>Gagal</small><strong>{{ number_format($failed) }}</strong></div>
    <div class="stat"><small>Refund</small><strong>{{ number_format($refunded) }}</strong></div>
</div>
<table>
    <thead><tr><th>Kategori</th><th>Jumlah Order</th><th>Pendapatan</th></tr></thead>
    <tbody>
    @forelse ($perCategory as $row)
        <tr><td>{{ $row['category'] }}</td><td>{{ number_format($row['total_orders']) }}</td><td>Rp {{ number_format((float) $row['total'], 0, ',', '.') }}</td></tr>
    @empty
        <tr><td colspan="3">Tidak ada data.</td></tr>
    @endforelse
    </tbody>
</table>
</body>
</html>
