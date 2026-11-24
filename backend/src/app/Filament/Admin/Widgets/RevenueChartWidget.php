<?php

namespace App\Filament\Admin\Widgets;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

/**
 * Grafik pemasukan harian 30 hari terakhir (hanya lihat, tanpa ekspor).
 */
class RevenueChartWidget extends ChartWidget
{
    use Concerns\VisibleToAdmin;

    protected static ?string $heading = 'Pemasukan 30 hari terakhir';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        $start = now()->subDays(29)->startOfDay();

        $totals = Payment::query()
            ->where('status', PaymentStatus::Paid)
            ->where('paid_at', '>=', $start)
            ->selectRaw('DATE(paid_at) as day, SUM(amount) as total')
            ->groupBy('day')
            ->pluck('total', 'day');

        $days = collect(range(29, 0))
            ->map(fn (int $offset): Carbon => now()->subDays($offset)->startOfDay());

        return [
            'datasets' => [
                [
                    'label' => 'Pemasukan (Rp)',
                    'data' => $days
                        ->map(fn (Carbon $day): float => (float) ($totals[$day->toDateString()] ?? 0))
                        ->all(),
                    'borderColor' => '#2F4B8F',
                    'backgroundColor' => 'rgba(47, 75, 143, 0.15)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
            ],
            'labels' => $days
                ->map(fn (Carbon $day): string => $day->translatedFormat('j M'))
                ->all(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'ticks' => [
                        'precision' => 0,
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
        ];
    }
}
