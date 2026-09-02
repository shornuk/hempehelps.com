<?php

namespace App\Enums;

enum ReviewStatus: string
{
    case Draft = 'draft';
    case Published = 'published';
    case Hidden = 'hidden';
}
