<?php

namespace App\Http\Controllers\Pc;

use AlicFeng\IdentityCard\InfoHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Pc\VisitorRequest;
use App\Http\Resources\Pc\VisitorResource;
use App\Models\Visitor;
use DB;

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
            ->with('ways', 'visitorType')
            ->withFiles()
            ->latest('id')
            ->paginate(request('pageSize', 10))
        );
    }

    public function store(VisitorRequest $visitorRequest)
    {
        $visitor = \DB::transaction(function() use ($visitorRequest){
            $validated = $visitorRequest->only(['name', 'visitor_type_id', 'id_card', 'phone', 'unit', 'reason', 'limiter', 'user_id', 'access_date', 'access_time', 'relation']);
            $validated['gender'] = InfoHelper::identityCard()->sex($visitorRequest->id_card) == 'M' ? '男' : '女';
            $validated['age'] = InfoHelper::identityCard()->age($visitorRequest->id_card);
            $visitor = Visitor::create($validated);
            $visitor->attachFiles($visitorRequest->face_picture_ids);
            $visitor->ways()->attach($visitorRequest->way_ids);
            return $visitor;
        });
        return send_data(new VisitorResource($visitor->load('ways', 'visitorType')->loadFiles()));
    }

    public function show(Visitor $visitor)
    {
        return send_data(new VisitorResource($visitor->load('ways', 'visitorType')->loadFiles()));
    }

    public function update(VisitorRequest $visitorRequest, Visitor $visitor)
    {
        $visitor = \DB::transaction(function() use ($visitorRequest, $visitor){
            $validated = $visitorRequest->only(['name', 'visitor_type_id', 'id_card', 'phone', 'unit', 'reason', 'limiter', 'user_id', 'access_date', 'access_time', 'relation']);
            $validated['gender'] = InfoHelper::identityCard()->sex($visitorRequest->id_card) == 'M' ? '男' : '女';
            $validated['age'] = InfoHelper::identityCard()->age($visitorRequest->id_card);
            $visitor->fill($validated)->save();
            $visitor->syncFiles($visitorRequest->face_picture_ids);
            $visitor->ways()->sync($visitorRequest->way_ids);
            return $visitor;
        });
        return send_data(new VisitorResource($visitor->load('ways', 'visitorType')->loadFiles()));
    }

    public function destroy(VisitorRequest $visitorRequest)
    {
        DB::transaction(function() use ($visitorRequest){
            $visitors = Visitor::findMany($visitorRequest->ids);
            foreach($visitors as $visitor){
                $visitor->detachFiles();
                $visitor->ways()->detach();
                $visitor->delete();
            }
        });
        return no_content();
    }
}
