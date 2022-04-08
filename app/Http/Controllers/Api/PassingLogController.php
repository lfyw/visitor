<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PassingLogRequest;
use App\Http\Resources\Api\PassingLogResource;
use App\Models\Gate;
use App\Models\PassingLog;
use App\Models\Visitor;

class PassingLogController extends Controller
{
    public function store(PassingLogRequest $request)
    {
        $visitor = Visitor::firstWhere('id_card', $request->id_card);

        $gate = Gate::firstWhere(['ip' => $request->ip]);

        $passingLog = PassingLog::create([
            'visitorable_type' => $visitor ? get_class($visitor) : Visitor::class,
            'visitorable_id' => $visitor ? $visitor->id : 0,
            'gate_id' => $gate->id,
            'passed_at' => now(),
        ]);
        return send_data(new PassingLogResource($passingLog->load('gate')));
    }
}
