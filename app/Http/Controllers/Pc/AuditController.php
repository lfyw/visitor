<?php

namespace App\Http\Controllers\Pc;

use App\Enums\AuditStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Pc\AuditRequest;
use App\Http\Resources\Pc\AuditResource;
use App\Models\Audit;
use App\Models\Role;
use Illuminate\Http\Response;

class AuditController extends Controller
{
    public function index()
    {
        return AuditResource::collection(Audit::with([
            'visitorType:id,name',
            'user:id,name,real_name,department_id',
            'user.department.ancestors',
            'ways',
            'auditors.user:id,name',
        ])->latest('id')->orderBy('audit_status')->paginate(\request('pageSize', 10)));
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
     * todo 审批通过后下发指令
     * @param Audit $audit
     * @param AuditRequest $auditRequest
     * @return \Illuminate\Support\Facades\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(Audit $audit, AuditRequest $auditRequest)
    {
        $this->authorize('update', $audit);

        if ($audit->audit_status !== AuditStatus::WAITING){
            return error('无法重复审批' ,Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if (auth()->user()->role->name !== Role::SUPER_ADMIN){
            $auditor = $audit->auditors()->where('user_id', $audit->id)->first();
            $auditor->fill([
                'suggestion' => $auditRequest->refused_reason,
                'audit_status' => $auditRequest->audit_status,
            ])->save();
        }
        $audit->fill([
            'access_time_from' => $auditRequest->access_time_from,
            'access_time_to' => $auditRequest->access_time_to,
            'limiter' => $auditRequest->limiter,
            'audit_status' => $auditRequest->audit_status,
            'refused_reason' => $auditRequest->refused_reason
        ])->save();

        return send_data(new AuditResource($audit->load([
            'visitorType:id,name',
            'user:id,name,real_name,department_id',
            'user.department.ancestors',
            'ways',
            'auditors.user:id,name',
        ])->loadFiles()));
    }

    public function destroy(Audit $audit)
    {
        $audit->delete();
        return no_content();
    }
}
