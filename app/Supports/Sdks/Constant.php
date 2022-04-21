<?php

namespace App\Supports\Sdks;

class Constant
{
    const SET_USER_PATH = '/setuser';
    const DEL_USER_PATH = '/deluser';

    public static function getSetUserUrl()
    {
        return config('services.issue.host') . ':' .  config('services.issue.port') . self::SET_USER_PATH;
    }

    public static function getDelUserUrl()
    {
        return  config('services.issue.host') . ':' . config('services.issue.port') . self::DEL_USER_PATH;
    }
}
