<?php

namespace App\Models;

use App\Observers\PassingLogObserver;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PassingLog extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function booted()
    {
        static::observe(PassingLogObserver::class);
    }

    public function visitor(): BelongsTo
    {
        return $this->belongsTo(Visitor::class, 'id_card', 'id_card');
    }

    public function gate(): BelongsTo
    {
        return $this->belongsTo(Gate::class);
    }

    public function scopeWhenIdCard(Builder $builder, $idCard): Builder
    {
        return $builder->when($idCard, fn(Builder $log) => $log->where('id_card', $idCard));
    }

}
