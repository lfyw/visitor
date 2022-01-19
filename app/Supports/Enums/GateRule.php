<?php

namespace App\Supports\Enums;


enum GateRule: string implements Enumable
{
    use HasEnum;

    case IN = '进';
    case OUT = '出';
}
