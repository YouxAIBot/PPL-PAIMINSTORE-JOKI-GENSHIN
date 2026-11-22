<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JokiCategory;
use App\Models\Order;
use App\Models\Payment;
use Dompdf\Dompdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        [$start, $end] = $this->range($request);

        return view('admin.reports.index', $this->payload($start, $end));
    }

    public function exportCsv(Request $request)
    {
        [$start, $end] = $this->range($request);
        $payload = $this->payload($start, $end);

        $lines = [
            'Laporan Paiminstore;',
            'Periode;'.$start->toDateString().' s.d. '.$end->toDateString().';',
            'Total pendapatan;'.number_format($payload['revenue'], 2, ',', '.').';',
            '',
            'Kategori;Jumlah Order;Pendapatan',
        ];

        foreach ($payload['perCategory'] as $row) {
            $lines[] = $row['category'].';'.$row['total_orders'].';'.number_format((float) $row['total'], 2, ',', '.');
        }

        $lines[] = '';
        $lines[] = 'Transaksi sukses;'.$payload['success'];
        $lines[] = 'Transaksi gagal;'.$payload['failed'];
        $lines[] = 'Refund penuh;'.$payload['refunded'];

        $csv = implode("\n", $lines);

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="laporan-paiminstore.csv"',
        ]);
    }

    public function exportPdf(Request $request)
    {
        [$start, $end] = $this->range($request);

        $html = view('admin.reports.pdf', $this->payload($start, $end))->render();

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="laporan-paiminstore.pdf"',
        ]);
    }

    private function range(Request $request): array
    {
        $start = $request->date('start') ? now()->parse($request->input('start'))->startOfDay() : now()->startOfWeek()->startOfDay();
        $end = $request->date('end') ? now()->parse($request->input('end'))->endOfDay() : now()->endOfWeek()->endOfDay();

        return [$start, $end];
    }

    private function payload($start, $end): array
    {
        $paid = Payment::where('status', Payment::PAID)->whereBetween('paid_at', [$start, $end]);

        $trend = collect(range(7, 0))->map(function ($i) {
            $week = now()->subWeeks($i);

            return [
                'label' => $week->format('d M'),
                'total' => (float) Payment::where('status', Payment::PAID)
                    ->whereBetween('paid_at', [$week->copy()->startOfWeek(), $week->copy()->endOfWeek()])
                    ->sum(DB::raw('amount - refunded_amount')),
            ];
        });

        $perCategory = DB::table('joki_categories')
            ->selectRaw('joki_categories.name AS category, COUNT(orders.id) AS total_orders, SUM(orders.total_amount) AS total')
            ->leftJoin('joki_packages', 'joki_packages.category_id', '=', 'joki_categories.id')
            ->leftJoin('orders', 'orders.package_id', '=', 'joki_packages.id')
            ->leftJoin('payments', 'payments.order_id', '=', 'orders.id')
            ->where('payments.status', Payment::PAID)
            ->whereBetween('payments.paid_at', [$start, $end])
            ->groupBy('joki_categories.id', 'joki_categories.name')
            ->get()
            ->map(fn ($row) => [
                'category' => $row->category,
                'total_orders' => (int) $row->total_orders,
                'total' => (float) $row->total,
            ]);

        return [
            'start' => $start,
            'end' => $end,
            'revenue' => (float) (clone $paid)->sum(DB::raw('amount - refunded_amount')),
            'perCategory' => $perCategory,
            'success' => (clone $paid)->count(),
            'failed' => Payment::where('status', Payment::FAILED)->whereBetween('created_at', [$start, $end])->count(),
            'refunded' => Payment::where('status', Payment::REFUNDED)->whereBetween('refunded_at', [$start, $end])->count(),
            'trend' => $trend,
            'maxTotal' => max(1, $trend->max('total')),
        ];
    }
}
