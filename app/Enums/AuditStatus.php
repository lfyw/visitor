<?php

namespace App\Enums;

use Lfyw\LfywEnum\Enumable;
use Lfyw\LfywEnum\HasEnum;

enum AuditStatus:int implements Enumable
{
    use HasEnum;

    case WAITING = 1;
    case PASS = 2;
    case REJECT = 3;
}
