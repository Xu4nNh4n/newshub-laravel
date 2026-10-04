<?php

namespace App\Enums;

enum PostModerationAction: string
{
    case Hide = 'hide';
    case Archive = 'archive';
    case Restore = 'restore';
    case Feature = 'feature';
    case Unfeature = 'unfeature';
}
