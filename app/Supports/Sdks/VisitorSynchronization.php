<?php

namespace App\Supports\Sdks;

use App\Enums\AuditStatus;
use App\Models\Audit;
use App\Models\Gate;
use App\Models\Passageway;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VisitorSynchronization
{
    public static function add(Audit $audit)
    {
        if ($audit->audit_status == AuditStatus::PASS){
            //开始下发
            $ways = $audit->ways;
            $passageways = Passageway::whereHas('ways', fn($way) => $way->whereIn('id', $ways->pluck('id')))->get();
            $gates = Gate::whereHas('passageways', fn($passageway) => $passageway->whereIn('id', $passageways->pluck('id')))->get(['ip', 'number'])->toArray();
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
            $setUser = Http::post(Constant::getSetUserUrl(), $parameter);
            if ($setUser->ok()){
                Log::info('闸机下放成功:', ['response' => $setUser->json(), 'parameter' => $parameter]);
            }
            if ($setUser->failed()){
                Log::error('闸机下方失败:', ['error' => $setUser->json(), 'message' => $parameter]);
            }
        }
    }

    public static function delete()
    {

    }
}
