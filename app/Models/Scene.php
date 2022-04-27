<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;

class Scene extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function visitor():BelongsTo
    {
        return $this->belongsTo(Visitor::class);
    }

    public function scopeOnlyToday(Builder $builder):Builder
    {
        return $builder->whereDay('created_at', today());
    }

    public static function in($visitorId, $wayId, $gateId, $passagewayId, $passedAt)
    {
        static::create([
            'visitor_id' => $visitorId,
            'way_id' => $wayId,
            'gate_id' => $gateId,
            'passageway_id' => $passagewayId,
            'passed_at' => $passedAt
        ]);
        Log::info('实时人员进入记录:', [
            'scene' => '今日当前区域总人数：' . Scene::whereDate('created_at', today())->count(),
            'visitor_id' => $visitorId,
            'way_id' => $wayId,
            'gate_id' => $gateId,
            'passageway_id' => $passagewayId,
            'passed_at' => $passedAt
        ]);
    }

    public static function out($visitorId, $passagewayId)
    {
        static::whereVisitorId($visitorId)->where('passageway_id', $passagewayId)->delete();
        Log::info('实时人员离开记录:', [
            'scene' => '今日当前区域总人数：' . Scene::whereDate('created_at',today())->count(),
        ]);
    }
}
