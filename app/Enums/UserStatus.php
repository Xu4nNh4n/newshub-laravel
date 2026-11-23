<?php

namespace App\Enums;

enum UserStatus: string
{
    case Active = 'active';
    case Blocked = 'blocked';

    /** Return the Vietnamese label shown in the interface. */
    public function label(): string
    {
        return match ($this) {
            self::Active => 'Hoạt động',
            self::Blocked => 'Đã khóa',
        };
    }
}
