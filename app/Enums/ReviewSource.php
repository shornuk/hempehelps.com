<?php

namespace App\Enums;

enum ReviewSource: string
{
    case Import = 'import';
    case Admin = 'admin';
    case Customer = 'customer';
}
