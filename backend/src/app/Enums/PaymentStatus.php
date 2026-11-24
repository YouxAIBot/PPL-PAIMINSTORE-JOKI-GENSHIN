<?php

namespace App\Enums;

/**
 * Status pembayaran (ikatan istilah baku: pending, paid, failed, refunded).
 */
enum PaymentStatus: string
{
    case Pending = 'pending';
    case Paid = 'paid';
    case Failed = 'failed';
    case Refunded = 'refunded';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Menunggu',
            self::Paid => 'Lunas',
            self::Failed => 'Gagal',
            self::Refunded => 'Dikembalikan',
        };
    }

    /**
     * Status final: webhook tidak boleh mengubahnya lagi (idempotensi).
     */
    public function isFinal(): bool
    {
        return in_array($this, [self::Paid, self::Failed, self::Refunded], true);
    }
}
