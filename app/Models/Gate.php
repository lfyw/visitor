<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gate extends Model
{
    use HasFactory;

    protected $guarded = [];

    public $timestamps = false;

    public function scopeWhenNumber(Builder $builder, $number):Builder
    {
        return $builder->when(filled($number), fn() => $builder->where('number', 'like', "%{$number}%"));
    }
}
