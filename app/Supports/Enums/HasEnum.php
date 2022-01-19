<?php

namespace App\Supports\Enums;

trait HasEnum
{
    public static function getNames():array
    {
        return array_column(static::cases(), 'name');
    }

    public static function getValues():array
    {
        return array_column(static::cases(), 'value');
    }

    public static function getDescriptions():array
    {
        return array_combine(static::getNames(), static::getValues());
    }

    public function getDescription():string|int
    {
        $descriptions = static::getDescriptions();
        return $descriptions[$this->name];
    }

    public function getName():string|int
    {
        return $this->name;
    }

    public function getValue():string|int
    {
        return $this->value;
    }
}
