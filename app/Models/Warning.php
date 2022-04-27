<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warning extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function scopeOnlyToday(Builder $builder): Builder
    {
        return $builder->whereDay('warning_at', today());
    }

    public function scopeWarningAtFrom(Builder $builder, $warningAtFrom): Builder
    {
        return $builder->when(filled($warningAtFrom), fn(Builder $warning) => $warning->where('warning_at', '>=', $warningAtFrom));
    }

    public function scopeWarningAtTo(Builder $builder, $warningAtTo)
    {
        return $builder->when(filled($warningAtTo), fn(Builder $warning) => $warning->where('warning_at', '<=', $warningAtTo));
    }
}
