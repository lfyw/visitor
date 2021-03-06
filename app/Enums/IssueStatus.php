<?php

namespace App\Enums;

use Lfyw\LfywEnum\Enumable;
use Lfyw\LfywEnum\HasEnum;
/**
 * ä¸ĺçść
 */
enum IssueStatus:int implements Enumable
{
    use HasEnum;

    case SUCCESS = 1;
    case FAILURE = 2;
    case PARTIAL_SUCCESS = 3;
}
