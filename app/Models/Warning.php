<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warning extends Model
{
    use HasFactory;

    protected $guarded = [];

    public static function scopeOnlyToday(Builder $builder)
    {
        return $builder->whereDay('warning_at', today());
    }
}
