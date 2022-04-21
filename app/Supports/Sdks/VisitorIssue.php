<?php

namespace App\Supports\Sdks;

use App\Models\Audit;
use App\Models\Gate;
use App\Models\Passageway;
use App\Models\Visitor;
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

        Log::info('【生产环境】临时访客审批通过 => 启动下放', ['audit' => $audit]);
        $passageways = Passageway::getByWays($audit->ways)->get();
        $gates = Gate::getByPassageways($passageways)->get(['ip'])->toArray();

        $facePicture = $audit->files()->first();
        $parameter = [
            'id_card' => $audit->id_card,
            'real_name' => $audit->name,
            'face_picture' => config('app.url') . $facePicture->url,
            'access_date_from' => $audit->access_date_from,
            'access_date_to' => $audit->access_date_to,
            'access_time_from' => $audit->access_time_from,
            'access_time_to' => $audit->access_time_to,
            'limiter' => $audit->limiter,
            'gate' => $gates,
        ];
        $response = Http::timeout(5)->post(Constant::getSetUserUrl(), $parameter);

        $response->throw();
        Log::info('【生产环境】临时访客下发情况', ['body' => $response->body(), 'json' => $response->json(), 'audit' => $audit]);

        return true;
    }

    public static function addByIdCard($idCard, $gates)
    {
        $visitor = Visitor::firstWhere('id_card', $idCard)->loadFiles();

        if (config('app.env') !== 'production') {
            Log::info('【测试环境】重新下放直接通过', ['id_card' => $idCard, 'visitor' => $visitor]);
            return true;
        }

        Log::info('【生产环境】重新下放', ['id_card' => $idCard, 'visitor' => $visitor]);

        $parameter = [
            'id_card' => $visitor->id_card,
            'real_name' => $visitor->name,
            'face_picture' => config('app.url') . $visitor->files()->first()?->url,
            'access_date_from' => $visitor->access_date_from,
            'access_date_to' => $visitor->access_date_to,
            'access_time_from' => $visitor->access_time_from,
            'access_time_to' => $visitor->access_time_to,
            'limiter' => $visitor->limiter,
            'gate' => $gates,
        ];
        Http::timeout(5)->post(Constant::getSetUserUrl(), $parameter);

        return true;
    }

    public static function delete($idCard, $gates = null)
    {
        $visitor = Visitor::firstWhere('id_card', $idCard)?->loadFiles();

        if (config('app.env') !== 'production') {
            Log::info('【测试环境】临时访客删除下放直接通过', ['id_card' => $idCard, 'visitor' => $visitor]);
            return true;
        }
        if (!$visitor) {
            return false;
        }

        Log::info('【生产环境】访客删除下发', ['id_card' => $idCard, 'visitor' => $visitor]);

        if (!$gates) {
            $gates = Gate::getByWaysThroughPassageway($visitor->ways)->get(['ip', 'number'])->toArray();
        }

        $parameter = [
            'id_card' => $visitor->id_card,
            'real_name' => $visitor->name,
            'face_picture' => config('app.url') . $visitor->files()->first()?->url,
            'access_date_from' => $visitor->access_date_from,
            'access_date_to' => $visitor->access_date_to,
            'access_time_from' => $visitor->access_time_from,
            'access_time_to' => $visitor->access_time_to,
            'limiter' => $visitor->limiter,
            'gate' => $gates,
        ];

        Http::timeout(5)->post(Constant::getDelUserUrl(), $parameter);

        return true;
    }

}
