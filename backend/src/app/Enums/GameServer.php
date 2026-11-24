<?php

namespace App\Enums;

/**
 * Server game pada kolom order_game_accounts.server.
 */
enum GameServer: string
{
    case Asia = 'asia';
    case America = 'america';
    case Europe = 'europe';
    case TwHkMo = 'twhkmo';

    public function label(): string
    {
        return match ($this) {
            self::Asia => 'Asia',
            self::America => 'America',
            self::Europe => 'Europe',
            self::TwHkMo => 'TW/HK/MO',
        };
    }
}
