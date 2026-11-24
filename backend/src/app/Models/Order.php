<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Order joki (ERD bagian 2.4).
 *
 * price_snapshot dan est_hours_snapshot menyimpan harga/jam saat pesanan dibuat,
 * sehingga perubahan paket tidak mengubah order lama.
 */
class Order extends Model
{
    protected $fillable = [
        'order_no',
        'user_id',
        'package_id',
        'booster_id',
        'status',
        'price_snapshot',
        'total_amount',
        'est_hours_snapshot',
        'queue_priority',
        'customer_notes',
        'cancel_reason',
        'paid_at',
        'started_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
            'price_snapshot' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'est_hours_snapshot' => 'integer',
            'queue_priority' => 'integer',
            'paid_at' => 'datetime',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    /** Pengguna (customer) pemilik order. */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** Booster yang ditugaskan, nullable. */
    public function booster(): BelongsTo
    {
        return $this->belongsTo(User::class, 'booster_id');
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(JokiPackage::class, 'package_id');
    }

    public function gameAccount(): HasOne
    {
        return $this->hasOne(OrderGameAccount::class, 'order_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'order_id');
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class, 'order_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'order_id');
    }
}
