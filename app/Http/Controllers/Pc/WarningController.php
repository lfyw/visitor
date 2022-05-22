<?php

namespace App\Http\Controllers\Pc;

use App\Enums\WarningStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Pc\WarningRequest;
use App\Http\Resources\Pc\WarningResource;
use App\Jobs\PushVisitor;
use App\Models\Scene;
use App\Models\Warning;

class WarningController extends Controller
{
    public function index()
    {
        return WarningResource::collection(Warning::name(\request('name'))
            ->type(\request('type'))
            ->warningAtFrom(\request('warning_at_from'))
            ->warningAtTo(\request('warning_at_to'))
            ->status(\request('status'))
            ->with([
                'visitor:id,access_count',
                'handler:id,name,real_name'
            ])
            ->latest('warning_at')
            ->paginate(\request('pageSize', 10)));
    }

    public function update(WarningRequest $request)
    {
        Warning::findMany(\request('ids'))->each(function (Warning $warning) use ($request) {
            if ($warning->status == WarningStatus::LEAVE->getValue()) {
                Scene::where('visitor_id', $warning->visitor_id)->delete();
            }
            $warning->fill([
                'handler_id' => auth()->id(),
                'status' => $request->status,
                'note' => $request->note,
                'handled_at' => now(),
            ])->save();

            PushVisitor::dispatch($warning->id_card,
                $warning->access_date_from,
                $warning->access_date_to,
                $warning->access_time_from,
                $warning->access_time_to,
                $warning->limiter
            )->onQueue('issue');
        });

    }
}
