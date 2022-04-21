<?php

namespace App\Http\Controllers\Pc;

use App\Enums\GateRule;
use App\Http\Controllers\Controller;
use App\Models\Passageway;
use App\Models\PassingLog;
use App\Models\Rule;
use Illuminate\Support\Facades\DB;

class BoardController extends Controller
{
    public function index()
    {

    }

    public function passagewayPassingChart()
    {
        $rule = Rule::first();
        //没有规则 => 退出
        if (!$rule){
            return [];
        }
        //没有看板规则 => 退出
        $boardRule = $rule->value['board'] ?? null;
        if (!$boardRule){
            return [];
        }

        $gatePassing = PassingLog::whenPassedAtFrom(\request('passed_at_from'))
            ->whenPassedAtTo(\request('passed_at_to'))
            ->selectRaw('gate_id, count(*) as passing_count')
            ->groupBy('gate_id')
            ->get();

        $chart = [];
        Passageway::with('gates')
            ->whereIn('id', $boardRule)
            ->get()
            ->each(function ($passageway) use (&$chart, $gatePassing){
                $gatesIn = $passageway->gates->where('rule', GateRule::IN->getValue());
                $gatesInCount = $gatePassing->whereIn('gate_id', $gatesIn->pluck('id'))->sum('passing_count');
                $gatesOut =  $passageway->gates->where('rule', GateRule::OUT->getValue());
                $gatesOutCount = $gatePassing->whereIn('gate_id', $gatesOut->pluck('id'))->sum('passing_count');
                $chart[] = [
                    'passageway' => $passageway->name,
                    'passing_in_count' => $gatesInCount ?: 0,
                    'passing_out_count' => $gatesOutCount ?: 0
                ];
            });
        return $chart;
    }

    public function passingTimeChart()
    {
        $passingLog = PassingLog::whenPassedAtFrom(\request('passed_at_from'))
            ->whenPassedAtTo(\request('passed_at_to'))
            ->selectRaw("to_char(passed_at, 'HH24:00') as time, count(*) as passing_count")
            ->groupBy('time')
            ->orderBy('time')
            ->get();
        return send_data($passingLog);
    }

    public function passingLogChart()
    {
        $personTime = [];
        $person = [];
        PassingLog::whenPassedAtFrom(\request('passed_at_from'))
            ->whenPassedAtTo(\request('passed_at_to'))
            ->groupBy('type')
            ->select(['type',
                DB::raw('count(id) as type_person_time_count'),
                DB::raw('count(distinct id_card) as type_count')
            ])
            ->get()
            ->each(function ($passingLog) use (&$personTime, &$person) {
                $personTime[] = [
                    'type' => $passingLog->type,
                    'type_person_time_count' => $passingLog->type_person_time_count
                ];
                $person[] = [
                    'type' => $passingLog->type,
                    'type_count' => $passingLog->type_count
                ];
            });

        $personTimeChart['total_count'] = collect($personTime)->sum('type_person_time_count');
        $personTimeChart['detail'] = $personTime;
        $personChart['total_count'] = collect($person)->sum('type_count');
        $personChart['detail'] = $person;
        return send_data(['person_time_chart' => $personTimeChart, 'person_chart' => $personChart]);
    }
}
