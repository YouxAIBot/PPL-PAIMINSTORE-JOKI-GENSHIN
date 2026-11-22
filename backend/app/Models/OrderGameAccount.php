<?php

namespace App\Models;

use App\Casts\VaultCredential;
use Illuminate\Database\Eloquent\Model;

class OrderGameAccount extends Model
{
    public $incrementing = false;

    protected $primaryKey = 'order_id';

    protected $fillable = [
        'order_id', 'uid', 'server', 'credential_encrypted', 'attachment_url', 'purge_at',
    ];

    protected $hidden = ['credential_encrypted'];

    protected function casts(): array
    {
        return [
            'credential_encrypted' => VaultCredential::class,
            'purge_at' => 'datetime',
        ];
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
