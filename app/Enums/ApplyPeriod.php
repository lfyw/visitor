<?php

namespace App\Enums;

use Lfyw\LfywEnum\Enumable;
use Lfyw\LfywEnum\HasEnum;
/**
 * 审批有效期
 */
enum ApplyPeriod:string implements Enumable
{
    use HasEnum;

    case DAY = 'day';
    case YEAR = 'year';
}
