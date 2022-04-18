<?php

namespace App\Http\Controllers\Pc;

use App\Events\OperationDone;
use App\Http\Controllers\Controller;
use App\Http\Requests\Pc\UserTypeRequest;
use App\Http\Resources\Pc\UserTypeResource;
use App\Models\OperationLog;
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
        event(new OperationDone(OperationLog::SETTING,
            sprintf("新增员工类型【%s】", $userTypeRequest->real_name),
            auth()->id()));
        return send_data(new UserTypeResource($userType));
    }

    public function show(UserType $userType)
    {
        return send_data(new UserTypeResource($userType));
    }

    public function update(UserType $userType, UserTypeRequest $userTypeRequest)
    {
        $userType->fill($userTypeRequest->validated())->save();
        event(new OperationDone(OperationLog::SETTING,
            sprintf("编辑员工类型【%s】", $userTypeRequest->real_name),
            auth()->id()));
        return send_data(new UserTypeResource($userType));
    }

    public function destroy(UserType $userType)
    {
        if($userType->users?->first()){
            return error(sprintf("人员类型 %s 已经关联了人员，请先解除关联", $userType->name), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $userType->delete();
        event(new OperationDone(OperationLog::SETTING,
            sprintf("删除员工类型"),
            auth()->id()));
        return no_content();
    }

    public function select()
    {
        return send_data(UserType::all(['id', 'name']));
    }
}
