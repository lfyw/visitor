<?php

namespace App\Http\Controllers\Pc;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pc\GateRequest;
use App\Http\Resources\Pc\GateResource;
use App\Models\Gate;

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
        Gate::destroy($gateRequest->ids);
        return no_content();
    }
}
