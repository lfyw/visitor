<?php

namespace App\Http\Controllers\Pc;

use App\Events\OperationDone;
use App\Http\Controllers\Controller;
use App\Http\Requests\Pc\VisitorSettingRequest;
use App\Http\Resources\Pc\VisitorSettingResource;
use App\Models\OperationLog;
use App\Models\VisitorSetting;
use Illuminate\Support\Facades\DB;

class VisitorSettingController extends Controller
{
    public function index()
    {
        return VisitorSettingResource::collection(VisitorSetting::with('ways', 'visitorType')->paginate(request('pageSize', 10)));
    }

    public function store(VisitorSettingRequest $visitorSettingRequest)
    {
        $visitorSetting = DB::transaction(function()use ($visitorSettingRequest){
            $visitorSetting = VisitorSetting::create($visitorSettingRequest->only(['visitor_type_id', 'apply_period', 'approver', 'visitor_limiter', 'visitor_relation']));
            $visitorSetting->ways()->attach($visitorSettingRequest->way_ids);
            return $visitorSetting;
        });

        event(new OperationDone(OperationLog::SETTING,
            sprintf("新增访客设置"),
            auth()->id()));
        return send_data(new VisitorSettingResource($visitorSetting->load('ways', 'visitorType')));
    }

    public function show(VisitorSetting $visitorSetting)
    {
        return send_data(new VisitorSettingResource($visitorSetting->load('ways', 'visitorType')));
    }

    public function update(VisitorSetting $visitorSetting, VisitorSettingRequest $visitorSettingRequest)
    {
        $visitorSetting = DB::transaction(function()use ($visitorSettingRequest, $visitorSetting){
            $visitorSetting->fill($visitorSettingRequest->only(['visitor_type_id', 'apply_period', 'approver', 'visitor_limiter', 'visitor_relation']))->save();
            $visitorSetting->ways()->sync($visitorSettingRequest->way_ids);
            return $visitorSetting;
        });
        event(new OperationDone(OperationLog::SETTING,
            sprintf("编辑访客设置"),
            auth()->id()));
        return send_data(new VisitorSettingResource($visitorSetting->load('ways', 'visitorType')));
    }

    public function destroy(VisitorSetting $visitorSetting)
    {
        $visitorSetting->ways()->detach();
        $visitorSetting->delete();
        event(new OperationDone(OperationLog::SETTING,
            sprintf("删除访客设置"),
            auth()->id()));
        return no_content();
    }

    public function select()
    {
        return  VisitorSettingResource::collection(VisitorSetting::with(['ways', 'visitorType'])->get());
    }
}
