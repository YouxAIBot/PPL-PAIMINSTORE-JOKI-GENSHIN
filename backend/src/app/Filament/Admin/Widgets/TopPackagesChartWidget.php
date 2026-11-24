<?php

namespace App\Filament\Admin\Widgets;

use App\Models\JokiPackage;
use Filament\Widgets\ChartWidget;

/**
 * Paket terlaris berdasarkan jumlah order (hanya lihat, tanpa ekspor).
 */
class TopPackagesChartWidget extends ChartWidget
{
    use Concerns\VisibleToAdmin;

    protected static ?string $heading = 'Paket terlaris (5 teratas)';

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        $packages = JokiPackage::query()
            ->withCount('orders')
            ->orderByDesc('orders_count')
            ->take(5)
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah order',
                    'data' => $packages->pluck('orders_count')->all(),
                    'backgroundColor' => [
                        '#2F4B8F',
                        '#4B6FC4',
                        '#7C97D9',
                        '#A9BCE8',
                        '#D3DDF4',
                    ],
                    'borderRadius' => 6,
                ],
            ],
            'labels' => $packages->pluck('name')->all(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
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
