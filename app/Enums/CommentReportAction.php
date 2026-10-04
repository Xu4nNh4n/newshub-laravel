<?php

namespace App\Enums;

enum CommentReportAction: string
{
    case Dismiss = 'dismiss';
    case Hide = 'hide';
    case Delete = 'delete';
}
