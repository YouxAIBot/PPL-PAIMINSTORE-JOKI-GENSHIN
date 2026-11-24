<?php

namespace App\Enums;

/**
 * Metode pembayaran pada kolom payments.method.
 */
enum PaymentMethod: string
{
    case Gateway = 'gateway';
    case Manual = 'manual';

    public function label(): string
    {
        return match ($this) {
            self::Gateway => 'Payment Gateway (Midtrans)',
            self::Manual => 'Transfer Manual',
        };
    }
}
