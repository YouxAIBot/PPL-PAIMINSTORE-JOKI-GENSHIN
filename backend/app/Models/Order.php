<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public const PENDING_PAYMENT = 'pending_payment';
    public const PAID = 'paid';
    public const QUEUED = 'queued';
    public const IN_PROGRESS = 'in_progress';
    public const COMPLETED = 'completed';
    public const CANCELLED = 'cancelled';
    public const REFUNDED = 'refunded';

    public const STATUSES = [
        self::PENDING_PAYMENT, self::PAID, self::QUEUED, self::IN_PROGRESS,
        self::COMPLETED, self::CANCELLED, self::REFUNDED,
    ];

    /** Status yang masih boleh dibatalkan admin. */
    public const CANCELLABLE = [
        self::PENDING_PAYMENT, self::PAID, self::QUEUED, self::IN_PROGRESS,
    ];

    protected $fillable = [
        'order_no', 'user_id', 'package_id', 'booster_id', 'status',
        'price_snapshot', 'total_amount', 'est_hours_snapshot', 'queue_priority',
        'customer_notes', 'cancel_reason', 'paid_at', 'started_at', 'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'price_snapshot' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'paid_at' => 'datetime',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function package()
    {
        return $this->belongsTo(JokiPackage::class);
    }

    public function booster()
    {
        return $this->belongsTo(User::class, 'booster_id');
    }

    public function gameAccount()
    {
        return $this->hasOne(OrderGameAccount::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function isCancellable(): bool
    {
        return in_array($this->status, self::CANCELLABLE, true);
    }

    public function scopeStatus(Builder $query, ?string $status): Builder
    {
        return $status ? $query->where('status', $status) : $query;
    }
}
