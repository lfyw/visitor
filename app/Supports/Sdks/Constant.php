<?php

namespace App\Supports\Sdks;

class Constant
{
    const HOST = '192.168.1.119';
    const PORT = '9003';
    const SET_USER_PATH = '/setuser';

    public static function getSetUserUrl()
    {
        return self::HOST . '/' . self::PORT .self::SET_USER_PATH;
    }

}
