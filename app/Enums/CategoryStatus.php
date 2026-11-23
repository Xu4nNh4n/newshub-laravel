<?php

namespace App\Enums;

enum CategoryStatus: string
{
    case Active = 'active';
    case Hidden = 'hidden';

    /** Return the Vietnamese label shown in the interface. */
    public function label(): string
    {
        return match ($this) {
            self::Active => 'Đang hiển thị',
            self::Hidden => 'Đã ẩn',
        };
    }
}
