<?php

namespace App\Enums;

use Lfyw\LfywEnum\Enumable;
use Lfyw\LfywEnum\HasEnum;

enum Export:string implements Enumable
{
    use HasEnum;

    case PASSING_LOG = 'passing_log';
}
