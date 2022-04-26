<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Lfyw\FileManager\Traits\HasFiles;

class Visitor extends Model
{
    use HasFactory, HasFiles;

    const USER = 1;
    const TEMPORARY = 2;

    protected $guarded = [];

    public function ways():BelongsToMany
    {
        return $this->belongsToMany(Way::class);
    }

    public function visitorType():BelongsTo
    {
        return $this->belongsTo(VisitorType::class);
    }

    public function user():BelongsTo
    {
        return $this->belongsTo(User::class, 'id_card', 'id_card');
    }

    public function scopeFromUser(Builder $builder): Builder
    {
        return $builder->where('type', Visitor::USER);
    }

    public function scopeFromTemporary(Builder $builder):Builder
    {
        return $builder->where('type', Visitor::TEMPORARY);
    }

    public function scopeWhenName(Builder $builder, $name):Builder
    {
        return $builder->when(filled($name) ,fn(Builder $visitor) => $visitor->where('name', 'like', "%{$name}%"));
    }

    public function scopeWhenIdCard(Builder $builder, $idCard):Builder
    {
        return $builder->when(filled($idCard), fn(Builder $visitor) => $visitor->where('id_card', 'like', "%{$idCard}%"));
    }

    public function scopeWhenVisitorTypeId(Builder $builder, $visitorTypeId):Builder
    {
        return $builder->when(filled($visitorTypeId), fn(Builder $visitor) => $visitor->where('visitor_type_id', $visitorTypeId));
    }

    public function scopeWhenAgeFrom(Builder $builder, $ageFrom):Builder
    {
        return $builder->when(filled($ageFrom), fn(Builder $visitor) => $visitor->where('age', '>=', $ageFrom));
    }

    public function scopeWhenAgeTo(Builder $builder, $ageTo):Builder
    {
        return $builder->when(filled($ageTo), fn(Builder $visitor) => $visitor->where('age', '<=', $ageTo));
    }

    public function scopeWhenWayId(Builder $builder, $wayId):Builder
    {
        return $builder->when(filled($wayId), fn(Builder $visitor) => $visitor->whereHas('ways', fn(Builder $way) => $way->where('id', $wayId)));
    }

    public function scopeWhenAccessDateFrom(Builder $builder, $accessDateFrom):Builder
    {
        return $builder->when(filled($accessDateFrom), fn(Builder $visitor) => $visitor->whereDate('access_date_from', '>=', $accessDateFrom));
    }

    public function scopeWhenAccessDateTo(Builder $builder, $accessDateTo):Builder
    {
        return $builder->when(filled($accessDateTo), fn(Builder $visitor) => $visitor->whereDate('access_date_to', '<=', $accessDateTo));
    }

    public function scopeNotInBlacklist(Builder $builder)
    {
        return $builder->where('is_in_blacklist', false);
    }

    public function blockBlacklist()
    {
        return $this->fill(['is_in_black_list' => true])->save();
    }

    public function cancelBlacklist()
    {
        return $this->fill(['is_in_black_list' => false])->save();
    }
}
