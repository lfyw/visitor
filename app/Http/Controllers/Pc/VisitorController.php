<?php

namespace App\Http\Controllers\Pc;

use AlicFeng\IdentityCard\InfoHelper;
use App\Events\OperationDone;
use App\Http\Controllers\Controller;
use App\Http\Requests\Pc\VisitorRequest;
use App\Http\Resources\Pc\VisitorResource;
use App\Models\OperationLog;
use App\Models\Visitor;
use App\Supports\Sdks\VisitorIssue;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class VisitorController extends Controller
{
    public function index()
    {
        return VisitorResource::collection(Visitor::whenName(request('name'))
            ->whenIdCard(request('id_card'))
            ->whenVisitorTypeId(request('visitor_type_id'))
            ->whenWayId(request('way_id'))
            ->whenAgeFrom(request('age_from'))
            ->whenAgeTo(request('age_to'))
            ->whenAccessDateFrom(request('access_date_from'))
            ->whenAccessDateTo(request('access_date_to'))
            ->notInBlacklist()
            ->with([
                'ways',
                'visitorType',
                'user:id,name,real_name,id_card,department_id',
                'user.department.ancestors'
            ])
//            ->fromTemporary()
            ->withFiles()
            ->latest('id')
            ->paginate(request('pageSize', 10))
        );
    }

    public function store(VisitorRequest $visitorRequest)
    {
        $visitor = \DB::transaction(function() use ($visitorRequest){
            $validated = Arr::except($visitorRequest->validated(), ['face_picture_ids', 'way_ids']);
            $validated['gender'] = InfoHelper::identityCard()->sex($visitorRequest->id_card) == 'M' ? '男' : '女';
            $validated['age'] = InfoHelper::identityCard()->age($visitorRequest->id_card);
            $visitor = Visitor::create($validated);
            $visitor->attachFiles($visitorRequest->face_picture_ids);
            $visitor->ways()->attach($visitorRequest->way_ids);
            return $visitor;
        });
        event(new OperationDone(OperationLog::VISITOR,
            sprintf("新增访客【%s】", $visitorRequest->name),
            auth()->id()));
        return send_data(new VisitorResource($visitor->load([
            'ways',
            'visitorType',
            'user:id,name,real_name,id_card,department_id',
            'user.department.ancestors'
        ])->loadFiles()));
    }

    public function show(Visitor $visitor)
    {
        return send_data(new VisitorResource($visitor->load([
            'ways',
            'visitorType',
            'user:id,name,real_name,id_card,department_id',
            'user.department.ancestors'
        ])->loadFiles()));
    }

    public function update(VisitorRequest $visitorRequest, Visitor $visitor)
    {
        $visitor = \DB::transaction(function() use ($visitorRequest, $visitor){
            $validated = Arr::except($visitorRequest->validated(), ['face_picture_ids', 'way_ids']);
            $validated['gender'] = InfoHelper::identityCard()->sex($visitorRequest->id_card) == 'M' ? '男' : '女';
            $validated['age'] = InfoHelper::identityCard()->age($visitorRequest->id_card);
            $visitor->fill($validated)->save();
            $visitor->syncFiles($visitorRequest->face_picture_ids);
            $visitor->ways()->sync($visitorRequest->way_ids);
            return $visitor;
        });
        event(new OperationDone(OperationLog::VISITOR,
            sprintf("编辑访客【%s】", $visitorRequest->name),
            auth()->id()));
        return send_data(new VisitorResource($visitor->load([
            'ways',
            'visitorType',
            'user:id,name,real_name,id_card,department_id',
            'user.department.ancestors'
        ])->loadFiles()));
    }

    public function destroy(VisitorRequest $visitorRequest)
    {
        DB::transaction(function() use ($visitorRequest){
            $visitors = Visitor::findMany($visitorRequest->ids);
            foreach($visitors as $visitor){
                VisitorIssue::delete($visitor->id_card);
                $visitor->detachFiles();
                $visitor->ways()->detach();
                $visitor->delete();
            }
        });
        event(new OperationDone(OperationLog::VISITOR,
            sprintf("删除访客"),
            auth()->id()));
        return no_content();
    }
}
