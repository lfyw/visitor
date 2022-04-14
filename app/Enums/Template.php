<?php

namespace App\Enums;

use Lfyw\LfywEnum\Enumable;
use Lfyw\LfywEnum\HasEnum;

enum Template:string implements Enumable
{
    use HasEnum;

    case DEPARTMENT = '部门导入模板';
    case USER = '人员导入模板';
    case VISITOR = '访客导入模板';
}
