<?php

namespace App\Http\Controllers\Api;

use AlicFeng\IdentityCard\InfoHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AuditRequest;
use App\Http\Resources\Api\AuditResource;
use App\Models\Audit;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Response;

class AuditController extends Controller
{
    public function index()
    {
        return AuditResource::collection(Audit::whereIdCard(\request('id_card'))->with([
            'user:id,name,department_id',
            'user.department.ancestors',
            'ways',
            'visitorType',
            'auditors.user:id,name',
        ])->latest()->paginate(\request('pageSize', 10)));
    }

    public function store(AuditRequest $auditRequest)
    {
        $audit = \DB::transaction(function () use ($auditRequest){
            $validated = \Arr::except($auditRequest->validated(), ['face_picture_ids', 'way_ids']);
            $validated['gender'] = InfoHelper::identityCard()->sex($auditRequest->id_card) == 'M' ? '男' : '女';
            $validated['age'] = InfoHelper::identityCard()->age($auditRequest->id_card);
            $audit = Audit::create($validated);

            $audit->ways()->attach($auditRequest->way_ids);
            $audit->attachFiles($auditRequest->face_picture_ids);

            // 添加审批人，根据配置文件寻找
            $visitorSetting = $audit->visitorType->visitorSettings()->whereHas('ways',fn(Builder $builder) => $builder->whereIn('id', $auditRequest->way_ids))->first();
            if (!$visitorSetting){
                return error('请现在后台添加对应的访客设置', Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            return $audit;
        });
        return send_data(new AuditResource($audit->fresh()->load([
            'user:id,name,department_id',
            'user.department.ancestors',
            'ways',
            'visitorType',
            'auditors.user:id,name',
        ])));
    }

}
