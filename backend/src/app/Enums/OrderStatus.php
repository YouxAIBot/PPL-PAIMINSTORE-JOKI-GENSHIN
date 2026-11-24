<?php

namespace App\Enums;

/**
 * Status order (ikatan istilah baku: pending_payment, queued, in_progress, completed, cancelled).
 *
 * Transisi yang divalidasi:
 *   pending_payment -> queued    (pembayaran diterima)
 *   pending_payment -> cancelled (pembayaran gagal / kedaluwarsa)
 * Transisi lain menyusul pada fase booster.
 */
enum OrderStatus: string
{
    case PendingPayment = 'pending_payment';
    case Queued = 'queued';
    case InProgress = 'in_progress';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::PendingPayment => 'Menunggu Pembayaran',
            self::Queued => 'Dalam Antrian',
            self::InProgress => 'Dikerjakan',
            self::Completed => 'Selesai',
            self::Cancelled => 'Dibatalkan',
        };
    }

    /**
     * Status yang sudah final: tidak boleh berubah lagi oleh webhook.
     */
    public function isFinal(): bool
    {
        return in_array($this, [self::Completed, self::Cancelled], true);
    }
}
