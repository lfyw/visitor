<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Passageway extends Model
{
    use HasFactory;

    protected $guarded = [];

    public $timestamps = false;

    public static function booted()
    {
        static::deleted(fn($passageway) => $passageway->gates()->detach());
    }

    public function gates():BelongsToMany
    {
        return $this->belongsToMany(Gate::class);
    }

    public function ways():BelongsToMany
    {
        return $this->belongsToMany(Way::class);
    }

    public function scopeWhenName(Builder $builder, $name):Builder
    {
        return $builder->when(filled($name), fn(Builder $builder) => $builder->where('name', 'like', "%{$name}%"));
    }

    public function scopeWhenGateNumber(Builder $builder, $gateNumber):Builder
    {
        return $builder->whereHas('gates', fn(Builder $gateBuilder) => $gateBuilder->whenNumber($gateNumber));
    }

    public function scopeGetByWays(Builder $builder, $ways):Builder
    {
        return $builder->whereHas('ways', fn(Builder $way) => $way->whereIn('id', $ways->pluck('id')));
    }
}
