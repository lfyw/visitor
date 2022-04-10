<?php

namespace App\Models;

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
        static::creating(function ($passingLog){
            $passingLogCount = PassingLog::whereIdCard($passingLog->id_card)->count();
            Visitor::whereIdCard($passingLog->id_card)?->fill(['access_count' => $passingLogCount])->save();
        });
    }

    public function gate(): BelongsTo
    {
        return $this->belongsTo(Gate::class);
    }

    public function scopeFilterByIdCard(Builder $builder, $idCard):Builder
    {
        return $builder->where('id_card', $idCard);
    }
}
