<?php

namespace App\Enums;

enum UserRole: string
{
    case User = 'user';
    case Author = 'author';
    case Admin = 'admin';
}
