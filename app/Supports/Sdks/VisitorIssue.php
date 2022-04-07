<?php

namespace App\Supports\Sdks;

use App\Models\Audit;
use App\Models\Gate;
use App\Models\Passageway;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VisitorIssue
{
    public static function add(Audit $audit)
    {
        if (config('app.env') !== 'production') {
            Log::info('【测试环境】临时访客下放直接通过', ['audit' => $audit]);
            return true;
        }

        Log::info('【生产环境】临时访客审批通过=>下放', ['audit' => $audit]);
        $passageways = Passageway::getByWays($audit->ways)->get();
        $gates = Gate::getByPassageways($passageways)->get(['ip', 'number'])->toArray();
        $parameter = [
            'id_card' => $audit->id_card,
            'real_name' => $audit->real_name,
            'face_picture' => $audit->face_picture,
            'access_date_from' => $audit->access_date_from,
            'access_date_to' => $audit->access_date_to,
            'access_time_from' => $audit->access_time_from,
            'access_time_to' => $audit->access_time_to,
            'limiter' => $audit->limiter,
            'gate' => $gates,
        ];
        Http::timeout(5)->post(Constant::getSetUserUrl(), $parameter);

        return true;
    }

    public static function delete()
    {

    }
}
