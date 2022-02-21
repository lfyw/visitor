<?php

namespace App\Http\Controllers\Pc;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pc\VisitorTypeRequest;
use App\Http\Resources\Pc\VisitorTypeResource;
use App\Models\VisitorType;

class VisitorTypeController extends Controller
{
    public function index()
    {
        return VisitorTypeResource::collection(VisitorType::paginate(request('pageSize', 10)));
    }

    public function store(VisitorTypeRequest $visitorTypeRequest)
    {
        $visitorType = VisitorType::create($visitorTypeRequest->validated());
        return send_data(new VisitorTypeResource($visitorType));
    }

    public function show(VisitorType $visitorType)
    {
        return send_data(new VisitorTypeResource($visitorType));
    }

    public function update(VisitorType $visitorType, VisitorTypeRequest $visitorTypeRequest)
    {
        $visitorType->fill($visitorTypeRequest->validated())->save();
        return send_data(new VisitorTypeResource($visitorType));
    }

    public function destroy(VisitorType $visitorType)
    {
        // if($visitorType->users->first()){
        //     return error(sprintf("人员类型 %s 已经关联了人员，请先解除关联", $userType->name), Response::HTTP_UNPROCESSABLE_ENTITY);
        // }
        $visitorType->delete();
        return no_content();
    }

    public function select()
    {
        return send_data(VisitorType::all());
    }
}