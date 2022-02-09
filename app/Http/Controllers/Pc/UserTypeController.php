<?php

namespace App\Http\Controllers\Pc;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pc\UserTypeRequest;
use App\Http\Resources\Pc\UserTypeResource;
use App\Models\UserType;

class UserTypeController extends Controller
{
    public function index()
    {
        return UserTypeResource::collection(UserType::paginate(request('pageSize', 10)));
    }

    public function store(UserTypeRequest $UserTypeRequest)
    {
        $UserType = UserType::create($UserTypeRequest->validated());
        return send_data(new UserTypeResource($UserType));
    }

    public function show(UserType $UserType)
    {
        return send_data(new UserTypeResource($UserType));
    }

    public function update(UserType $UserType, UserTypeRequest $UserTypeRequest)
    {
        $UserType->fill($UserTypeRequest->validated())->save();
        return send_data(new UserTypeResource($UserType));
    }

    public function destroy(UserType $UserType)
    {
        $UserType->delete();
        return no_content();
    }
}
