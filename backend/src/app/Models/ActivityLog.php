<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Audit log API (ERD bagian 2.10).
 * Berbeda dari activity_log milik panel Filament; keduanya dipertahankan.
 */
class ActivityLog extends Model
{
    protected $fillable = [
        'actor_id',
        'action',
        'entity_type',
        'entity_id',
        'meta',
        'ip',
    ];

    protected function casts(): array
    {
        return [
            'meta' => 'array',
        ];
    }

    /** Pelaku aksi; null untuk aksi sistem (misalnya webhook). */
    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }
}
