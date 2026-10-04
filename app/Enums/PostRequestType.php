<?php

namespace App\Enums;

enum PostRequestType: string
{
    case Removal = 'removal';
    case Correction = 'correction';
}
