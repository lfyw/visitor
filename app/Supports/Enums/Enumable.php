<?php

namespace App\Supports\Enums;

interface Enumable
{
    public static function getNames():array;

    public static function getValues():array;

    public static function getDescriptions():array;

    public function getDescription():string|int;

    public function getName():string|int;

    public function getValue():string|int;
}
