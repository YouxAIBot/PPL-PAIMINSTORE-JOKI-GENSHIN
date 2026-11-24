<?php

namespace App\Models;

use App\Enums\GameServer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Kredensial akun game untuk satu order (ERD bagian 2.5, relasi 1:1).
 *
 * credential_encrypted disimpan terenkripsi AES-256-GCM dan tidak pernah
 * dikirim balik lewat API ($hidden).
 */
class OrderGameAccount extends Model
{
    protected $table = 'order_game_accounts';

    protected $fillable = [
        'order_id',
        'uid',
        'server',
        'credential_encrypted',
        'attachment_url',
        'purge_at',
    ];

    /** Kredensial tidak pernah ikut di-serialize. */
    protected $hidden = [
        'credential_encrypted',
    ];

    protected $primaryKey = 'order_id';

    public $incrementing = false;

    protected function casts(): array
    {
        return [
            'server' => GameServer::class,
            'purge_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
