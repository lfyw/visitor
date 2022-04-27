<?php

namespace App\Enums;

use Lfyw\LfywEnum\Enumable;
use Lfyw\LfywEnum\HasEnum;

enum WarningStatus:int implements Enumable
{
    use HasEnum;

    case LEAVE = 1;
    case NOT_LEAVE = 2;
    case AT_DISPOSAL = 3;
}
