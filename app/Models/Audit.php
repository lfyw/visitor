<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Lfyw\FileManager\Traits\HasFiles;

class Audit extends Model
{
    use HasFactory, HasFiles, SoftDeletes;

    protected $guarded = [];

    public function ways(): BelongsToMany
    {
        return $this->belongsToMany(Way::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function visitorType(): BelongsTo
    {
        return $this->belongsTo(VisitorType::class);
    }

    public function auditors(): HasMany
    {
        return $this->hasMany(Auditor::class);
    }

    public function scopeName(Builder $builder, $name): Builder
    {
        return $builder->when(filled($name), fn(Builder $audit) => $audit->where('name', 'like', "%{$name}%"));
    }

    public function scopeIdCard(Builder $builder, $idCard): Builder
    {
        return $builder->when(filled($idCard), fn(Builder $audit) => $audit->where('id_card', 'like', "%{$idCard}%"));
    }

    public function scopeAuditStatus(Builder $builder, $auditStatus): Builder
    {
        return $builder->when(filled($auditStatus), fn(Builder $audit) => $audit->where('audit_status', $auditStatus));
    }

    public function scopeWayId(Builder $builder, $wayId): Builder
    {
        return $builder->when($wayId, fn(Builder $audit) => $audit->whereHas('ways', fn(Builder $way) => $way->where('id', $wayId)));
    }

    public function scopeAccessDateFrom(Builder $builder, $accessDateFrom): Builder
    {
        return $builder->when($accessDateFrom, fn(Builder $audit) => $audit->where('access_date_from', '>=', $accessDateFrom));
    }

    public function scopeAccessDateTo(Builder $builder, $accessDateTo): Builder
    {
        return $builder->when($accessDateTo, fn(Builder $audit) => $audit->where('access_date_to', '<=', $accessDateTo));
    }
}
