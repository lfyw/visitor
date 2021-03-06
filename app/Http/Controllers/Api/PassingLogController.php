<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PassingLogRequest;
use App\Http\Resources\Api\PassingLogResource;
use App\Models\Gate;
use App\Models\PassingLog;
use App\Models\Visitor;
use App\Supports\Sdks\VisitorIssue;
use Illuminate\Support\Str;

class PassingLogController extends Controller
{
    public function store(PassingLogRequest $request)
    {
        $gate = Gate::firstWhere(['ip' => $request->ip]);
        $idCard = Str::upper($request->id_card);
        //todo 测试逻辑，正式服测完要清掉
        if (!$gate) {
            $gate = Gate::create([
                'number' => '2022-04-08' . random_int(1000, 9999),
                'type' => 'ZJXH' . random_int(10000, 99999),
                'ip' => $request->ip,
                'location' => '综合楼',
                'rule' => '进'
            ]);
        }
//        if ($request->file('snapshot')) {
//            $path = $request->file('snapshot')->store(now()->format('Y-m') . '/' . now()->format('d'), 'snapshot');
//        }
        $passingLog = PassingLog::create([
            'id_card' => sm4encrypt($idCard),
            'gate_id' => $gate->id,
            'passed_at' => now(),
//            'snapshot' => $path ?? null
        ]);

        return send_data(new PassingLogResource($passingLog->load('gate')));
    }

    public function withSnapShot(PassingLogRequest $request)
    {
        $gate = Gate::firstWhere(['ip' => $request->ip]);
        $idCard = sm4encrypt(Str::upper($request->id_card));
        //todo 测试逻辑，正式服测完要清掉
        if (!$gate) {
            $gate = Gate::create([
                'number' => '2022-04-08' . random_int(1000, 9999),
                'type' => 'ZJXH' . random_int(10000, 99999),
                'ip' => $request->ip,
                'location' => '综合楼',
                'rule' => '进'
            ]);
        }
        if ($request->file('snapshot')) {
            $path = $request->file('snapshot')->store(now()->format('Y-m') . '/' . now()->format('d'), 'snapshot');
        }
        $passingLog = PassingLog::create([
            'id_card' => $idCard,
            'gate_id' => $gate->id,
            'passed_at' => $request->passed_at,
            'snapshot' => $path ?? null
        ]);

        //通行一次，对应访客数量累加一次通行记录
        $visitor = Visitor::firstWhere('id_card', $idCard);
        $visitor->increment('actual_pass_count');
        if ($visitor->actual_pass_count >= $visitor->limiter){
            VisitorIssue::delete(sm4decrypt($visitor->id_card));
        }

        return send_data(new PassingLogResource($passingLog->load('gate')));
    }
}
