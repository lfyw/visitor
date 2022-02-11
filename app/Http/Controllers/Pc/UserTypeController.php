<?php

namespace App\Http\Controllers\Pc;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pc\UserTypeRequest;
use App\Http\Resources\Pc\UserTypeResource;
use App\Models\UserType;
use Illuminate\Http\Response;

class UserTypeController extends Controller
{
    public function index()
    {
        return UserTypeResource::collection(UserType::paginate(request('pageSize', 10)));
    }

    public function store(UserTypeRequest $userTypeRequest)
    {
        $userType = UserType::create($userTypeRequest->validated());
        return send_data(new UserTypeResource($userType));
    }

    public function show(UserType $userType)
    {
        return send_data(new UserTypeResource($userType));
    }

    public function update(UserType $userType, UserTypeRequest $userTypeRequest)
    {
        $userType->fill($userTypeRequest->validated())->save();
        return send_data(new UserTypeResource($userType));
    }

    public function destroy(UserType $userType)
    {
        if($userType->users->first()){
            return error(sprintf("人员类型 %s 已经关联了人员，请先解除关联", $userType->name), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $userType->delete();
        return no_content();
    }
}
