<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Way extends Model
{
    use HasFactory;

    protected $guarded = [];

    public $timestamps = false;

    public static function booted()
    {
        static::deleted(function(Way $way){
            $way->passageways()->detach();
            $way->users()->detach();
            $way->visitors()->detach();
            $way->visitorSettings()->detach();
        });
    }

    public function users():BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function passageways():BelongsToMany
    {
        return $this->belongsToMany(Passageway::class);
    }

    public function visitors():BelongsToMany
    {
        return $this->belongsToMany(Visitor::class);
    }

    public function visitorSettings():BelongsToMany
    {
        return $this->belongsToMany(VisitorSetting::class);
    }

    public function scopeWhenName(Builder $builder, $name):Builder
    {
        return $builder->when(filled($name), fn(Builder $builder) => $builder->where('name', 'like', "%{$name}%"));
    }

    public function scopeWhenPassagewayName(Builder $builder, $passagewayName):Builder
    {
        return $builder->whereHas('passageways', fn(Builder $passagewayBuilder) => $passagewayBuilder->whenName($passagewayName));
    }
}
