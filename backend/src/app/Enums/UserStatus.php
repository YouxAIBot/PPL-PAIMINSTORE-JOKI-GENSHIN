<?php

namespace App\Enums;

/**
 * Status akun pengguna pada kolom users.status.
 */
enum UserStatus: string
{
    case Active = 'active';
    case Banned = 'banned';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Aktif',
            self::Banned => 'Diblokir',
        };
    }
}
