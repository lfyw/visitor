<?php

namespace App\Http\Controllers\Pc;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pc\GateRequest;
use App\Http\Resources\Pc\GateResource;
use App\Models\Gate;
use Illuminate\Http\Response;

class GateController extends Controller
{
    public function index()
    {
        return GateResource::collection(Gate::whenNumber(request('number'))->orderByDesc('id')->paginate(request('pageSize', 10)));
    }

    public function store(GateRequest $gateRequest)
    {
        $gate = Gate::create($gateRequest->validated());
        return send_data(new GateResource($gate));
    }

    public function show(Gate $gate)
    {
        return send_data(new GateResource($gate));
    }

    public function update(GateRequest $gateRequest, Gate $gate)
    {
        $gate->fill($gateRequest->validated())->save();
        return send_data(new GateResource($gate));
    }

    public function destroy(GateRequest $gateRequest)
    {
        $gates = Gate::findMany($gateRequest->ids);
        foreach($gates as $gate){
            if($gate->passageways?->first()){
                return error(sprintf("%s 通道关联此闸门，请先解除关联", implode(',', $$gate->passageways->pluck('name')->toArray())), Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        }
        $gates->delete();
        return no_content();
    }

    public function select()
    {
        return send_data(GateResource::collection(Gate::all()));
    }
}
