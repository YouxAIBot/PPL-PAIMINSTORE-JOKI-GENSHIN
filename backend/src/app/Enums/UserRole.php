<?php

namespace App\Enums;

/**
 * Peran pengguna pada kolom users.role (ERD bagian 2.3).
 */
enum UserRole: string
{
    case Customer = 'customer';
    case Booster = 'booster';
    case Admin = 'admin';

    public function label(): string
    {
        return match ($this) {
            self::Customer => 'Pengguna',
            self::Booster => 'Booster',
            self::Admin => 'Admin/Owner',
        };
    }
}
