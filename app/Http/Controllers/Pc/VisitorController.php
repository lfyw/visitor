<?php

namespace App\Http\Controllers\Pc;

use AlicFeng\IdentityCard\InfoHelper;
use App\Events\OperationDone;
use App\Http\Controllers\Controller;
use App\Http\Requests\Pc\VisitorRequest;
use App\Http\Resources\Pc\VisitorResource;
use App\Jobs\PullIssue;
use App\Models\OperationLog;
use App\Models\Visitor;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class VisitorController extends Controller
{
    public function index()
    {
        return VisitorResource::collection(Visitor::whenName(request('name'))
            ->whenIdCard(sm4encrypt(request('id_card')))
            ->whenPhone(sm4encrypt(request('phone')))
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
            ->canSee()
            ->withFiles()
            ->latest('id')
            ->paginate(request('pageSize', 10))
        );
    }

    public function store(VisitorRequest $visitorRequest)
    {
        $visitor = \DB::transaction(function () use ($visitorRequest) {
            $validated = Arr::except($visitorRequest->validated(), ['face_picture_ids', 'way_ids']);
            $validated['gender'] = InfoHelper::identityCard()->sex($visitorRequest->id_card) == 'M' ? '男' : '女';
            $validated['age'] = InfoHelper::identityCard()->age($visitorRequest->id_card);
            $validated['id_card'] = sm4encrypt(Str::upper($validated['id_card']));
            $validated['phone'] = sm4encrypt($validated['phone']);
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
        $visitor = \DB::transaction(function () use ($visitorRequest, $visitor) {
            $validated = Arr::except($visitorRequest->validated(), ['face_picture_ids', 'way_ids']);
            $validated['gender'] = InfoHelper::identityCard()->sex($visitorRequest->id_card) == 'M' ? '男' : '女';
            $validated['age'] = InfoHelper::identityCard()->age($visitorRequest->id_card);
            $validated['id_card'] = sm4encrypt(Str::upper($validated['id_card']));
            $validated['phone'] = sm4encrypt($validated['phone']);
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
        $visitors = Visitor::findMany($visitorRequest->ids);
        foreach ($visitors as $visitor) {
            PullIssue::dispatch(
                sm4decrypt($visitor->id_card),
                $visitor->name,
                $visitor->files->first()?->url,
                $visitor->access_date_from,
                $visitor->access_date_to,
                $visitor->access_time_from,
                $visitor->access_time_to,
                $visitor->limiter,
                $visitor->ways
            )->onQueue('issue');
            $visitor->syncFiles(clear: false);
            $visitor->ways()->detach();
            $visitor->delete();
        }
        event(new OperationDone(OperationLog::VISITOR,
            sprintf("删除访客"),
            auth()->id()));
        return no_content();
    }
}
