<?php

namespace App\Http\Controllers\Api;

use AlicFeng\IdentityCard\InfoHelper;
use App\Enums\ApproverType;
use App\Exceptions\MissingVisitorSettingException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AuditRequest;
use App\Http\Resources\Api\AuditResource;
use App\Models\Audit;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AuditController extends Controller
{
    public function index()
    {
        return AuditResource::collection(Audit::accessDateFrom(request('access_date_from'))
            ->accessDateTo(request('access_date_to'))
            ->whereIdCard(sm4encrypt(\request('id_card')))
            ->with([
                'user:id,name,real_name,department_id',
                'user.department.ancestors',
                'ways',
                'visitorType',
                'auditors.user:id,name,real_name',
            ])->latest()->paginate(\request('pageSize', 10)));
    }

    public function store(AuditRequest $auditRequest)
    {
        //5.2 如果日期超过一天，也中止下发
        $start = Carbon::parse($auditRequest->access_date_from);
        $end = Carbon::parse($auditRequest->access_date_to);
        if ($end->floatDiffInRealDays($start) > 2){
            return error('临时访客访问日期不能超过2天', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $audit = DB::transaction(function () use ($auditRequest) {
            $validated = Arr::except($auditRequest->validated(), ['face_picture_ids', 'way_ids']);
            $validated['gender'] = InfoHelper::identityCard()->sex($auditRequest->id_card) == 'M' ? '男' : '女';
            $validated['age'] = InfoHelper::identityCard()->age($auditRequest->id_card);
            $validated['id_card'] = sm4encrypt(\Str::upper($validated['id_card']));
            $validated['phone'] = sm4encrypt($validated['phone']);
            $validated['limiter'] = 10;
            $audit = Audit::create($validated);

            $audit->ways()->attach($auditRequest->way_ids);
            $audit->attachFiles($auditRequest->face_picture_ids);

            // 添加审批人，根据配置文件寻找
            $visitorSetting = $audit->visitorType->visitorSettings()->first();
            throw_unless($visitorSetting, new MissingVisitorSettingException('请先在后台添加对应的访客设置', Response::HTTP_NOT_FOUND));
            $visitorSetting = $audit->visitorType->visitorSettings()->whereHas('ways', fn(Builder $builder) => $builder->whereIn('id', $auditRequest->way_ids))->first();
            throw_unless($visitorSetting, new MissingVisitorSettingException('该类型访客不允许通行您申请访问的路线，请联系后台管理员添加', Response::HTTP_NOT_FOUND));

            collect($visitorSetting->approver)->sortBy('order')->each(function ($approver) use ($audit) {
                if ($approver['type'] == ApproverType::INTERVIEWEE->getValue()) {
                    $audit->auditors()->create([
                        'user_id' => $audit->user_id,
                        'user_real_name' => User::find($audit->user_id)->real_name
                    ]);
                }
                if ($approver['type'] == ApproverType::ROLE->getValue()) {
                    $roler = User::whereRoleId($approver['role_id'])->first();
                    $audit->auditors()->create([
                        'user_id' => $roler->id,
                        'user_real_name' => $roler->real_name
                    ]);
                }
            });
            return $audit;
        });
        return send_data(new AuditResource($audit->load([
            'user:id,name,department_id',
            'user.department.ancestors',
            'ways',
            'visitorType',
            'auditors.user:id,name',
        ])));
    }

    public function history()
    {
        if (!$audit = Audit::latest()->firstWhere('id_card', sm4encrypt(request('id_card')))){
            return error('未查询到历史信息', Response::HTTP_NOT_FOUND);
        }
        return send_data(new AuditResource($audit->load([
            'user:id,name,department_id',
            'user.department.ancestors',
            'ways',
            'visitorType',
            'auditors.user:id,name',
        ])->loadFiles()));
    }
}
