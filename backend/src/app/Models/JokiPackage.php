<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Paket joki yang dijual (ERD bagian 2.2).
 */
class JokiPackage extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'description',
        'price',
        'estimated_hours',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'estimated_hours' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(JokiCategory::class, 'category_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'package_id');
    }
}
