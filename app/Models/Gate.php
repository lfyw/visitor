<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Gate extends Model
{
    use HasFactory;

    protected $guarded = [];

    public $timestamps = false;

    public function passageways(): BelongsToMany
    {
        return $this->belongsToMany(Passageway::class);
    }

    public function scopeWhenNumber(Builder $builder, $number): Builder
    {
        return $builder->when(filled($number), fn() => $builder->where('number', 'like', "%{$number}%"));
    }

    public function scopeGetByPassageways(Builder $builder, $passageways)
    {
        return $builder->whereHas('passageways', fn(Builder $passageway) => $passageway->whereIn('id', $passageways->pluck('id')));
    }

    public function createIssue($visitorable, $issueStatus)
    {
        return Issue::create([
            'visitorable_type' => get_class($visitorable),
            'visitorable_id' => $visitorable->id,
            'gate_id' => $this->id,
            'issue_status' => $issueStatus,
            'gate_number' => $this->number,
            'gate_ip' => $this->ip,
            'rule' => $this->rule
        ]);
    }
}
