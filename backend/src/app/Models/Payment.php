<?php

namespace App\Models;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Pembayaran untuk satu order (ERD bagian 2.6).
 *
 * gateway_ref unique menjadi dasar idempotensi webhook Midtrans.
 */
class Payment extends Model
{
    protected $fillable = [
        'order_id',
        'method',
        'gateway_ref',
        'amount',
        'status',
        'proof_url',
        'refunded_amount',
        'verified_by',
        'paid_at',
        'refunded_at',
    ];

    protected function casts(): array
    {
        return [
            'method' => PaymentMethod::class,
            'status' => PaymentStatus::class,
            'amount' => 'decimal:2',
            'refunded_amount' => 'decimal:2',
            'paid_at' => 'datetime',
            'refunded_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    /** Admin yang memverifikasi pembayaran manual. */
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
