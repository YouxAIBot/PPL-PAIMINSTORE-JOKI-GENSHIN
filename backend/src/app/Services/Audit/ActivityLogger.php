<?php

namespace App\Services\Audit;

use App\Models\ActivityLog;

/**
 * Penulis audit log ke tabel activity_logs (ERD bagian 2.10).
 * Dipanggil dari service, bukan dari controller.
 */
class ActivityLogger
{
    /**
     * @param  array<string, mixed>  $meta
     */
    public function record(
        ?int $actorId,
        string $action,
        string $entityType,
        int $entityId,
        array $meta = [],
        ?string $ip = null,
    ): ActivityLog {
        return ActivityLog::create([
            'actor_id' => $actorId,
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'meta' => $meta,
            'ip' => $ip,
        ]);
    }
}
