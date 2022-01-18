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
        $visitorType->delete();
        return no_content();
    }
}
