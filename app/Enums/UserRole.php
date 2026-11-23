<?php

namespace App\Enums;

enum UserRole: string
{
    case User = 'user';
    case Author = 'author';
    case Admin = 'admin';

    /** Return the Vietnamese label shown in the interface. */
    public function label(): string
    {
        return match ($this) {
            self::User => 'Độc giả',
            self::Author => 'Tác giả',
            self::Admin => 'Quản trị viên',
        };
    }
}
