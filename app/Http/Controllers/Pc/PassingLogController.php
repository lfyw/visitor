<?php

namespace App\Http\Controllers\Pc;

use App\Http\Controllers\Controller;
use App\Http\Resources\Pc\PassingLogResource;
use App\Models\PassingLog;
use App\Models\UserType;
use App\Models\VisitorType;

class PassingLogController extends Controller
{
    public function index()
    {
        return PassingLogResource::collection(PassingLog::whenIdCard(\request('id_card'))
            ->whenName(request('name'))
            ->whenType(request('type'))
            ->whenPassagewayId(request('passageway_id'))
            ->whenGateId(request('gate_id'))
            ->whenRule(request('rule'))
            ->whenPassedAtFrom(request('passed_at_from'))
            ->whenPassedAtTo(request('passed_at_to'))
            ->with([
                'gate.passageways',
                'visitor.visitorType',
                'visitor.user.userType',
            ])
            ->latest('id')
            ->paginate(\request('pageSize', 10))
        );
    }

    public function select()
    {
        $type = collect();
        $visitorType = VisitorType::all()->pluck('name');
        $userType = UserType::all()->pluck('name');
        $type = $type->merge($visitorType);
        $type = $type->merge($userType);
        $type = $type->map(function ($value){
             return [
                 'key' => $value,
                 'value' => $value
             ];
        });
        return send_data($type);
    }
}
