<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

class ActivityLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'actor_id', 'action', 'entity_type', 'entity_id', 'meta', 'ip', 'created_at',
    ];

    protected function casts(): array
    {
        return ['meta' => 'array', 'created_at' => 'datetime'];
    }

    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    public static function record(
        ?User $actor,
        string $action,
        ?object $entity = null,
        array $meta = [],
        ?string $ip = null
    ): self {
        return static::create([
            'actor_id' => $actor?->id,
            'action' => $action,
            'entity_type' => $entity ? class_basename($entity) : null,
            'entity_id' => $entity?->getKey(),
            'meta' => $meta ?: null,
            'ip' => $ip ?? Request::ip(),
            'created_at' => now(),
        ]);
    }
}
