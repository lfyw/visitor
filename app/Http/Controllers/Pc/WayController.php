<?php

namespace App\Http\Controllers\Pc;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pc\WayRequest;
use App\Http\Resources\Pc\WayResource;
use App\Models\Way;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class WayController extends Controller
{
    public function index()
    {
        return WayResource::collection(
            Way::whenName(request('name'))
            ->whenPassagewayName(request('passageway_name'))
            ->with('passageways')
            ->latest('id')
            ->paginate(request('pageSize', 10))
        );
    }

    public function store(WayRequest $wayRequest)
    {
        $way = DB::transaction(function() use ($wayRequest){
            $way = Way::create(Arr::only($wayRequest->validated(), ['name', 'note']));
            $way->passageways()->attach($wayRequest->passageway_ids);
            return $way;
        });
        return send_data(new WayResource($way->load('passageways')));
    }

    public function show(Way $way)
    {
        return send_data(new WayResource($way->load('passageways')));
    }

    public function update(Way $way, WayRequest $wayRequest)
    {
        $way = DB::transaction(function() use ($wayRequest, $way){
            $way->fill(Arr::only($wayRequest->validated(), ['name', 'note']))->save();
            $way->passageways()->sync($wayRequest->passageway_ids);
            return $way;
        });
        return send_data(new WayResource($way->load('passageways')));
    }

    public function destroy(WayRequest $wayRequest)
    {
        $ways = Way::findMany($wayRequest->ids);
        $invalidWayNames = [];
        $invalidWayIds = [];
        foreach($ways as $way){
            if($way->users->first()){
                array_push($invalidWayNames, $way->name);
                array_push($invalidWayIds, $way->id);
            }
        }
        $ways->whereNotIn('id', $invalidWayIds)->each->delete();

        return $invalidWayIds
            ? send_message(sprintf("路线 %s 已关联人员，请先解除对应关联", implode(',', $invalidWayNames)), Response::HTTP_OK)
            : no_content();
    }
}
