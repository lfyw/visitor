<?php

namespace App\Http\Controllers\Pc;

use App\Enums\GateRule;
use App\Http\Controllers\Controller;
use App\Models\Passageway;
use App\Models\PassingLog;
use App\Models\Rule;
use App\Models\Scene;
use App\Models\User;
use App\Models\UserType;
use App\Models\Visitor;
use App\Models\Warning;
use Illuminate\Support\Facades\DB;

class BoardController extends Controller
{
    public function index()
    {
        $totalPersonCount = Scene::onlyToday()
            ->distinct()
            ->select('visitor_id')
            ->count();

        $visitors = Visitor::whereIn('id', Scene::onlyToday()->pluck('visitor_id'))->get();
        $visitorsFromUser = $visitors->where('type', Visitor::USER);
        $visitorsFromTemporary = $visitors->where('type', Visitor::TEMPORARY);
        $temporaryVisitorCount = $visitorsFromTemporary->count();
        $users = User::whereIn('id_card', $visitorsFromUser->pluck('id_card'))
            ->groupBy('user_type_id')
            ->selectRaw('user_type_id, count(id) as type_person_count')
            ->get();
        $userTypes = UserType::pluck('name', 'id')->toArray();
        $typePersonCount[] = [
            'type' => '临时访客',
            'person_count' => $temporaryVisitorCount
        ];
        $users->each(function (User $user) use ($userTypes, &$typePersonCount) {
            $typePersonCount[] = [
                'type' => $userTypes[$user->user_type_id],
                'person_count' => $user->type_person_count
            ];
        });

        $atDisposalWarningCount = Warning::whereNull('status')->count();

        return send_data([
            'total_person_count' => $totalPersonCount,
            'type_person_count' => $typePersonCount,
            'at_disposal_warning_count' => $atDisposalWarningCount ?: 0,
        ]);
    }

    public function passagewayPassingChart()
    {
        $rule = Rule::first();
        //没有规则 => 退出
        if (!$rule) {
            return [];
        }
        //没有看板规则 => 退出
        $boardRule = $rule->value['board'] ?? null;
        if (!$boardRule) {
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
            ->each(function ($passageway) use (&$chart, $gatePassing) {
                $gatesIn = $passageway->gates->where('rule', GateRule::IN->getValue());
                $gatesInCount = $gatePassing->whereIn('gate_id', $gatesIn->pluck('id'))->sum('passing_count');
                $gatesOut = $passageway->gates->where('rule', GateRule::OUT->getValue());
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

    public function warning()
    {
        $warnings = Warning::warningAtFrom(request('warning_at_from'))
            ->warningAtTo(request('warning_at_to'))
            ->groupBy('status')
            ->selectRaw('status, count(id) as warning_count')
            ->get()
            ->map(function (Warning $warning) {
                $warning->status = ($warning->status == null ? '未处置' : '已处置');
                return $warning;
            });
        return $warnings;
    }
}
