<?php

namespace App\Models;

use App\Enums\WarningStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Warning extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function visitor(): BelongsTo
    {
        return $this->belongsTo(Visitor::class);
    }

    public function handler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handler_id', 'id');
    }

    public function scopeOnlyToday(Builder $builder): Builder
    {
        return $builder->whereDay('warning_at', today());
    }

    public function scopeWarningAtFrom(Builder $builder, $warningAtFrom): Builder
    {
        return $builder->when(filled($warningAtFrom), fn(Builder $warning) => $warning->where('warning_at', '>=', $warningAtFrom));
    }

    public function scopeWarningAtTo(Builder $builder, $warningAtTo): Builder
    {
        return $builder->when(filled($warningAtTo), fn(Builder $warning) => $warning->where('warning_at', '<=', $warningAtTo));
    }

    public function scopeName(Builder $builder, $name): Builder
    {
        return $builder->when(filled($name), fn(Builder $builder) => $builder->where('name', 'like', "%{$name}%"));
    }

    public function scopeType(Builder $builder, $type): Builder
    {
        return $builder->when(filled($type), fn(Builder $builder) => $builder->where('type', $type));
    }

    public function scopeStatus(Builder $builder, $status): Builder
    {
        return $builder->when(filled($status), function (Builder $builder) use ($status) {
            dump($status);
            if ($status == WarningStatus::AT_DISPOSAL->getValue()) {
                return $builder->whereNull('status');
            }
            return $builder->where('status', $status);
        });
    }
}
