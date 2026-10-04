<?php

namespace App\Enums;

enum PostRequestPriority: string
{
    case Normal = 'normal';
    case Urgent = 'urgent';
}
