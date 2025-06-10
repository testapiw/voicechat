<?php

namespace App\Enum;

enum UserStatus: string
{
    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
}