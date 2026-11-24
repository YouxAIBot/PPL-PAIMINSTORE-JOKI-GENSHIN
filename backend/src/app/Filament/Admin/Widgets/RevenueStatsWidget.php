<?php

namespace App\Filament\Admin\Widgets;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Payment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

/**
 * Ringkasan penjualan pada dashboard (hanya lihat, tanpa ekspor).
 */
class RevenueStatsWidget extends BaseWidget
{
    use Concerns\VisibleToAdmin;

    protected static ?int $sort = 1;

    protected ?string $heading = 'Ringkasan penjualan';

    protected function getStats(): array
    {
        $paid = Payment::query()->where('status', PaymentStatus::Paid);

        $totalRevenue = (float) (clone $paid)->sum('amount');
        $monthRevenue = (float) (clone $paid)
            ->whereBetween('paid_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->sum('amount');

        $ordersThisMonth = Order::query()
            ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->count();
        $pendingPayment = Order::query()
            ->where('status', OrderStatus::PendingPayment)
            ->count();

        return [
            Stat::make('Total pemasukan', $this->rupiah($totalRevenue))
                ->description('Dari pembayaran berstatus lunas')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
            Stat::make('Pemasukan bulan ini', $this->rupiah($monthRevenue))
                ->description(now()->translatedFormat('F Y'))
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            Stat::make('Order bulan ini', (string) $ordersThisMonth)
                ->description('Semua status')
                ->descriptionIcon('heroicon-m-clipboard-document-list')
                ->color('info'),
            Stat::make('Menunggu pembayaran', (string) $pendingPayment)
                ->description('Belum dibayar')
                ->descriptionIcon('heroicon-m-clock')
                ->color($pendingPayment > 0 ? 'warning' : 'gray'),
        ];
    }

    private function rupiah(float $value): string
    {
        return 'Rp ' . number_format($value, 0, ',', '.');
    }
}
