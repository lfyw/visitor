<?php

namespace App\Enums;

use Lfyw\LfywEnum\Enumable;
use Lfyw\LfywEnum\HasEnum;

enum RoleEnum:string implements Enumable
{
    use HasEnum;

    case ADMIN = '超级管理员';
    case SYSTEM_ADMIN = '系统管理员';
    case DEPARTMENT_ADMIN = '部门管理员';
    case EMPLOYEE = '内部员工';
}
