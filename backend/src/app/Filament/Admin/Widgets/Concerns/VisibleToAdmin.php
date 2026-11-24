<?php

namespace App\Filament\Admin\Widgets\Concerns;

use App\Enums\UserRole;

/**
 * Widget laporan hanya untuk Admin/Owner (super_admin) dan pengguna ber-role admin.
 */
trait VisibleToAdmin
{
    public static function canView(): bool
    {
        $user = auth()->user();

        return $user !== null
            && ($user->hasRole('super_admin') || $user->role === UserRole::Admin);
    }
}
