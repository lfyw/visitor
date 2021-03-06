<?php

namespace App\Http\Controllers\Pc;

use App\Events\OperationDone;
use App\Http\Controllers\Controller;
use App\Http\Requests\Pc\WayRequest;
use App\Http\Resources\Pc\WayResource;
use App\Models\OperationLog;
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
        event(new OperationDone(OperationLog::WAY,
            sprintf("新增路线【%s】", $wayRequest->name),
            auth()->id()));
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
        event(new OperationDone(OperationLog::WAY,
            sprintf("编辑路线【%s】", $wayRequest->name),
            auth()->id()));
        return send_data(new WayResource($way->load('passageways')));
    }

    public function destroy(WayRequest $wayRequest)
    {
        Way::destroy($wayRequest->ids);
        event(new OperationDone(OperationLog::WAY,
            sprintf("删除路线"),
            auth()->id()));
        return no_content();
    }

    public function select()
    {
        return send_data(Way::all(['id', 'name']));
    }
}
