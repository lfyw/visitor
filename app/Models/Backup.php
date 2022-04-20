<?php

namespace App\Models;

use App\Observers\BackupObserver;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Backup extends Model
{
    use HasFactory;

    protected $guarded = [];

    public static function booted()
    {
        static::observe(BackupObserver::class);
    }

    public function scopeName(Builder $builder, $name): Builder
    {
        return $builder->when(filled($name), fn(Builder $backup) => $backup->where('name', 'like', "%{$name}%"));
    }

    public function scopeCreatedAtFrom(Builder $builder, $createdAtFrom): Builder
    {
        return $builder->when(filled($createdAtFrom), fn(Builder $backup) => $backup->where('created_at_from', '>=', $createdAtFrom));
    }

    public function scopeCreatedAtTo(Builder $builder, $createdAtTo): Builder
    {
        return $builder->when(filled($createdAtTo), fn(Builder $backup) => $backup->where('created_at_to', '<=', $createdAtTo));
    }
}
