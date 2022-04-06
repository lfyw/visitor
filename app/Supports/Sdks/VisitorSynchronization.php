<?php

namespace App\Supports\Sdks;

use App\Enums\AuditStatus;
use App\Models\Audit;
use App\Models\Gate;
use App\Models\Passageway;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VisitorSynchronization
{
    public static function add(Audit $audit)
    {
        if ($audit->audit_status == AuditStatus::PASS->getValue()){
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
            try {
                $setUser = Http::timeout(5)->post(Constant::getSetUserUrl(), $parameter);
                if ($setUser->ok()){
                    Log::info('闸机下放成功:', ['response' => $setUser->json(), 'parameter' => $parameter]);
                }else if ($setUser->failed()){
                    Log::error('闸机下放失败:', ['error' => $setUser->json(), 'parameter' => $parameter]);
                }
            }catch (ConnectionException $exception){
                Log::error('闸机下放异常:', ['error' => $exception->getMessage(), 'parameter' => $parameter]);
            }
        }
    }

    public static function delete()
    {

    }
}
