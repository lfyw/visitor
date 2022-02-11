<?php

namespace App\Http\Controllers\Pc;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pc\DepartmentRequest;
use App\Http\Resources\Pc\DepartmentResource;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Response;

class DepartmentController extends Controller
{
    public function index()
    {
        return Department::all()->toTree();
    }

    public function store(DepartmentRequest $departmentRequest)
    {
        $department = Department::create($departmentRequest->validated());
        return send_data(new DepartmentResource($department), Response::HTTP_CREATED);
    }

    public function show(Department $department)
    {
        return send_data(new DepartmentResource($department));
    }

    public function update(Department $department, DepartmentRequest $departmentRequest)
    {
        $department->fill($departmentRequest->validated())->save();
        return send_data(new DepartmentResource($department));
    }

    public function destroy(Department $department)
    {
        $descendantIds = Department::descendantsAndSelf($department->id)->pluck('id');
        if(User::whereIn('id', $descendantIds)->exists()){
            return error("单位或其下级单位已关联人员，请先解除关联", Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $department->delete();
        return no_content();
    }
}
