<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PassingLogRequest;
use App\Http\Resources\Api\PassingLogResource;
use App\Models\Gate;
use App\Models\PassingLog;
use Illuminate\Support\Str;

class PassingLogController extends Controller
{
    public function store(PassingLogRequest $request)
    {
        $gate = Gate::firstWhere(['ip' => $request->ip]);
        $idCard = Str::upper($request->id_card);
        //todo 测试逻辑，正式服测完要清掉
        if (!$gate){
            $gate = Gate::create([
                'number' => '2022-04-08' . random_int(1000, 9999),
                'type' => 'ZJXH' . random_int(10000, 99999),
                'ip' => $request->ip,
                'location' => '综合楼',
                'rule' => '进'
            ]);
        }
        $passingLog = PassingLog::create([
            'id_card' => $idCard,
            'gate_id' => $gate->id,
            'passed_at' => now(),
        ]);

        return send_data(new PassingLogResource($passingLog->load('gate')));
    }
}
