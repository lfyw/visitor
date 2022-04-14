<?php

namespace App\Enums;

use Lfyw\LfywEnum\Enumable;
use Lfyw\LfywEnum\HasEnum;

enum Import: string implements Enumable
{
    use HasEnum;

    case DEPARTMENT = 'department';
    case USER = 'user';
    case VISITOR = 'visitor';
}
