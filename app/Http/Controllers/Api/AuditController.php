<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AuditRequest;
use App\Http\Resources\Api\AuditResource;
use App\Models\Audit;
use Illuminate\Http\Request;

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
            $audit = Audit::create(\Arr::except($auditRequest->validated(), ['face_picture_ids', 'way_ids']));
            $audit->ways()->attach($auditRequest->way_ids);
            $audit->attachFiles($auditRequest->face_picture_ids);

            $audit->auditors()->create([
                'user_id' => $auditRequest->user_id,
            ]);
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
