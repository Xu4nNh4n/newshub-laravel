<?php

namespace App\Enums;

enum CommentModerationAction: string
{
    case Hide = 'hide';
    case Restore = 'restore';
    case Delete = 'delete';
}
