<?php

namespace App\Enums;

use Lfyw\LfywEnum\Enumable;
use Lfyw\LfywEnum\HasEnum;
/**
 * 审批人类型
 */
enum ApproverType:string implements Enumable
{
    use HasEnum;

    case ROLE = 'role';
    case INTERVIEWEE = 'interviewee';
}
