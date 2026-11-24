<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Percakapan chat pengguna dengan admin (ERD bagian 2.7).
 * Satu pengguna punya satu percakapan; pesan per order dibedakan lewat messages.order_id.
 */
class Conversation extends Model
{
    protected $fillable = [
        'user_id',
        'last_message_at',
        'admin_unread',
        'user_unread',
    ];

    protected function casts(): array
    {
        return [
            'last_message_at' => 'datetime',
            'admin_unread' => 'integer',
            'user_unread' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'conversation_id');
    }
}
