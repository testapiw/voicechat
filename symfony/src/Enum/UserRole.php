<?php

namespace App\Enum;

enum UserRole: string
{
    case GUEST = 'ROLE_GUEST';
    case USER = 'ROLE_USER';
    case ENGINEER = 'ROLE_ENGINEER';
    case INVESTOR = 'ROLE_INVESTOR';
    case MARKETER = 'ROLE_MARKETER';
    case INNOVATOR = 'ROLE_INNOVATOR';

    public static function values(): array
    {
        return array_map(fn(self $r) => $r->value, self::cases());
    }
}
