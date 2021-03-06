<?php

namespace App\Models;

use App\Enums\WarningStatus;
use App\Jobs\PullIssue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;

class Scene extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function visitor(): BelongsTo
    {
        return $this->belongsTo(Visitor::class);
    }

    public function gate(): BelongsTo
    {
        return $this->belongsTo(Gate::class,);
    }

    public function passageway(): BelongsTo
    {
        return $this->belongsTo(Passageway::class,);
    }

    public function way(): BelongsTo
    {
        return $this->belongsTo(Way::class);
    }

    public function scopeOnlyToday(Builder $builder): Builder
    {
        return $builder->whereDay('created_at', today());
    }

    public static function in($visitorId, $wayId, $gateId, $passagewayId, $passedAt)
    {
        static::updateOrCreate([
            'visitor_id' => $visitorId,
        ],[
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

    public static function out($visitorId, $passagewayId, $passingLog)
    {
        if (static::whereVisitorId($visitorId)->where('passageway_id', $passagewayId)->first()) {
            static::whereVisitorId($visitorId)->where('passageway_id', $passagewayId)->delete();
            Log::info('实时人员离开记录:', [
                'scene' => '今日当前区域总人数：' . Scene::whereDate('created_at', today())->count(),
            ]);
        } else {
            Log::info('实时人员无进有出:', [
                'scene' => '今日当前区域总人数：' . Scene::whereDate('created_at', today())->count(),
            ]);
            $visitor = Visitor::find($visitorId);
            //有出无进预警，取消下发
            Warning::create([
                'name' => $visitor->name,
                'type' => $visitor->getType(),
                'gender' => $visitor->gender,
                'age' => $visitor->age,
                'id_card' => $visitor->id_card,
                'phone' => $visitor->phone,
                'unit' => $visitor->unit,
                'user_real_name' => $visitor->getUserName(),
                'user_department' => $visitor->getUserDepartment(),
                'reason' => $visitor->reason,
                'access_date_from' => $visitor->access_date_from,
                'access_date_to' => $visitor->access_date_to,
                'ways' => $visitor->ways->pluck('name')->implode(','),
                'gate_name' => $passingLog?->gate->number,
                'gate_ip' => $passingLog?->gate->ip,
                'access_time_from' => $visitor->access_time_from,
                'access_time_to' => $visitor->access_time_to,
                'limiter' => $visitor->limiter,
                'relation' => $visitor->relation,
                'status' => WarningStatus::AT_DISPOSAL->getValue(),
                'warning_type' => '有出无进',
                'warning_at' => now(),
                'visitor_id' => $visitor->id
            ]);

            PullIssue::dispatch(
                sm4decrypt($visitor->id_card),
                $visitor->name,
                $visitor->files->first()?->url,
                $visitor->access_date_from,
                $visitor->access_date_to,
                $visitor->access_time_from,
                $visitor->access_time_to,
                $visitor->limiter,
                $visitor->ways
            )->onQueue('issue');
        }

    }
}
