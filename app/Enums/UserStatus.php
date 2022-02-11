<?php

namespace App\Enums;

use Lfyw\LfywEnum\Enumable;
use Lfyw\LfywEnum\HasEnum;

/**
 * 人员状态
 */
enum UserStatus:string implements Enumable
{
    use HasEnum;

    case DIMISSION = '离职';
    case EMPLOYMENT = '在职';
}
