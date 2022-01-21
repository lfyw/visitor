<?php

namespace App\Enums;

use Lfyw\LfywEnum\Enumable;
use Lfyw\LfywEnum\HasEnum;

enum GateRule:string implements Enumable
{
    use HasEnum;

    case IN = '进';
    case OUT = '出';
}
