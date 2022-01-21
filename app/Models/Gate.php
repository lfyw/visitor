<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Gate extends Model
{
    use HasFactory;

    protected $guarded = [];

    public $timestamps = false;

    public static function booted()
    {
        static::deleted(fn(Gate $gate) => $gate->passageways()->detach());
    }

    public function passageways():BelongsToMany
    {
        return $this->belongsToMany(Passageway::class);
    }

    public function scopeWhenNumber(Builder $builder, $number):Builder
    {
        return $builder->when(filled($number), fn() => $builder->where('number', 'like', "%{$number}%"));
    }
}
