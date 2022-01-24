<?php

namespace App\Http\Controllers\Pc;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pc\DepartmentRequest;
use App\Http\Resources\Pc\DepartmentResource;
use App\Models\Department;
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
        $department->delete();
        return no_content();
    }
}
