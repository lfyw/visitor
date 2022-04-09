<?php

namespace App\Http\Controllers\Pc;

use AlicFeng\IdentityCard\InfoHelper;
use App\Enums\AuditStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Pc\AuditRequest;
use App\Http\Resources\Pc\AuditResource;
use App\Models\Audit;
use App\Models\Gate;
use App\Models\Passageway;
use App\Models\Role;
use App\Models\Visitor;
use App\Supports\Sdks\VisitorIssue;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class AuditController extends Controller
{
    public function index()
    {
        return AuditResource::collection(Audit::name(request('name'))
            ->idCard(request('id_card'))
            ->auditStatus(request('audit_status'))
            ->wayId(request('way_id'))
            ->accessDateFrom(request('access_date_from'))
            ->accessDateTo(request('access_date_to'))
            ->with([
                'visitorType:id,name',
                'user:id,name,real_name,department_id',
                'user.department.ancestors',
                'ways',
                'auditors.user:id,name',
            ])
            ->withFiles()
            ->latest('id')->orderBy('audit_status')->paginate(\request('pageSize', 10)));
    }

    public function show(Audit $audit)
    {
        return send_data(new AuditResource($audit->load([
            'visitorType:id,name',
            'user:id,name,real_name,department_id',
            'user.department.ancestors',
            'ways',
            'auditors.user:id,name',
        ])->loadFiles()));
    }

    /**
     * @param Audit $audit
     * @param AuditRequest $auditRequest
     * @return \Illuminate\Support\Facades\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(Audit $audit, AuditRequest $auditRequest)
    {
        //1.确认审核权限
        $this->authorize('update', $audit);
        //2.确认审批状态
        if ($audit->audit_status !== AuditStatus::WAITING->value) {
            return error('无法重复审批', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        //3.填充审核人信息
        if (auth()->user()->role->name !== Role::SUPER_ADMIN) {
            $auditor = $audit->auditors()->where('user_id', $audit->id)->first();
            $auditor->fill([
                'suggestion' => $auditRequest->refused_reason,
                'audit_status' => $auditRequest->audit_status,
            ])->save();
        } else {
            $audit->auditors()->create([
                'audit_id' => $audit->id,
                'user_id' => auth()->id(),
                'suggestion' => $auditRequest->refused_reason,
                'audit_status' => $auditRequest->audit_status,
            ]);
        }
        //4.填充审批信息
        $validated = Arr::except($auditRequest->validated(), ['face_picture_ids', 'way_ids']);
        $validated['gender'] = InfoHelper::identityCard()->sex($auditRequest->id_card) == 'M' ? '男' : '女';
        $validated['age'] = InfoHelper::identityCard()->age($auditRequest->id_card);

        $audit->fill($validated)->save();
        $audit->ways()->sync($auditRequest->way_ids);
        $audit->syncFiles($auditRequest->face_picture_ids);

        //5. 审批拒绝则直接中止下发
        if ($audit->audit_status !== AuditStatus::PASS->getValue()) {
            return send_data(new AuditResource($audit->load([
                'visitorType:id,name',
                'user:id,name,real_name,department_id',
                'user.department.ancestors',
                'ways',
                'auditors.user:id,name',
            ])->loadFiles()));
        }

        //6.更新或创建访客信息+路线+照片
        $visitor = Visitor::updateOrCreate([
            'id_card' => $audit->id_card
        ], [
            'name' => $audit->name,
            'visitor_type_id' => $audit->visitor_type_id,
            'gender' => $audit->gender,
            'age' => $audit->age,
            'phone' => $audit->phone,
            'unit' => $audit->unit,
            'reason' => $audit->reason,
            'relation' => $audit->reason,
            'user_id' => $audit->user_id,
            'limiter' => $audit->limiter,
            'access_date_from' => $audit->access_date_from,
            'access_date_to' => $audit->access_date_to,
            'access_time_from' => $audit->access_time_from,
            'access_time_to' => $audit->access_time_to,
        ]);
        $visitor->ways()->sync($auditRequest->way_ids);
        $visitor->syncFiles($auditRequest->face_picture_ids);

        //7.下发
        $passageways = Passageway::getByWays($audit->ways)->get();
        $gates = Gate::getByPassageways($passageways)->get();
        try {
            //下发请求
            VisitorIssue::add($audit);
            //成功则记录下发成功记录
            $gates->each->createIssue($visitor->id_card, true);
            return send_data(new AuditResource($audit->load([
                'visitorType:id,name',
                'user:id,name,real_name,department_id',
                'user.department.ancestors',
                'ways',
                'auditors.user:id,name',
            ])->loadFiles()));
        } catch (\Exception $exception) {
            Log::error('下发异常:' . $exception->getMessage());
            //失败则记录下发失败记录
            $gates->each->createIssue($visitor->id_card, false);
            return send_message('网络异常', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(Audit $audit)
    {
        $audit->delete();
        return no_content();
    }
}
