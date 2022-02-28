<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blacklist extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function scopeName(Builder $builder, $name):Builder
    {
        return $builder->when(filled($name), fn(Builder $blacklist) => $blacklist->where('name', 'like', "%{$name}%"));
    }

    public function scopeIdCard(Builder $builder, $idCard):Builder
    {
        return $builder->when(filled($idCard), fn(Builder $blacklist) => $blacklist->where('id_card', 'like', "%{$idCard}%"));
    }
}
