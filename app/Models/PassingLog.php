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

    public function scopeWhenPhone(Builder $builder, $phone): Builder
    {
        return $builder->when($phone, fn(Builder $log) => $log->where('phone', $phone));
    }

    public function scopeWhenName(Builder $builder, $name): Builder
    {
        return $builder->when($name, fn(Builder $log) => $log->where('name', 'like', "%{$name}%"));
    }

    public function scopeWhenType(Builder $builder, $type): Builder
    {
        return $builder->when($type, fn(Builder $log) => $log->where('type', $type));
    }

    public function scopeWhenPassagewayId(Builder $builder, $passagewayId): Builder
    {
        return $builder->when($passagewayId, function (Builder $log) use ($passagewayId) {
            $log->whereHas('gate', function (Builder $gate) use ($passagewayId) {
                $gate->whereHas('passageways', fn(Builder $passageway) => $passageway->where('id', $passagewayId));
            });
        });
    }

    public function scopeWhenGateId(Builder $builder, $gateId): Builder
    {
        return $builder->when($gateId, fn(Builder $log) => $log->where('gate_id', $gateId));
    }

    public function scopeWhenRule(Builder $builder, $rule): Builder
    {
        return $builder->when($rule, fn(Builder $log) => $log->whereHas('gate', fn(Builder $log) => $log->where('rule', $rule)));
    }

    public function scopeWhenPassedAtFrom(Builder $builder, $passedAtFrom): Builder
    {
        return $builder->when($passedAtFrom, fn(Builder $log) => $log->where('passed_at', '>=', $passedAtFrom));
    }

    public function scopeWhenPassedAtTo(Builder $builder, $passedAtTo): Builder
    {
        return $builder->when($passedAtTo, fn(Builder $log) => $log->where('passed_at', '<=', $passedAtTo));
    }
}
