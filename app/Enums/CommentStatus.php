<?php

namespace App\Enums;

enum CommentStatus: string
{
    case Visible = 'visible';
    case Hidden = 'hidden';

    /** Return the Vietnamese label shown in the interface. */
    public function label(): string
    {
        return match ($this) {
            self::Visible => 'Đang hiển thị',
            self::Hidden => 'Đã ẩn',
        };
    }
}
