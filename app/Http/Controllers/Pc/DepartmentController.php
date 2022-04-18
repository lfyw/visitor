<?php

namespace App\Http\Controllers\Pc;

use App\Events\OperationDone;
use App\Http\Controllers\Controller;
use App\Http\Requests\Pc\DepartmentRequest;
use App\Http\Resources\Pc\DepartmentResource;
use App\Models\Department;
use App\Models\OperationLog;
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
        event(new OperationDone(OperationLog::DEPARTMENT,
            sprintf(sprintf("新增部门【%s】", $department->name)),
            auth()->id()));
        return send_data(new DepartmentResource($department), Response::HTTP_CREATED);
    }

    public function show(Department $department)
    {
        return send_data(new DepartmentResource($department));
    }

    public function update(Department $department, DepartmentRequest $departmentRequest)
    {
        $department->fill($departmentRequest->validated())->save();
        event(new OperationDone(OperationLog::DEPARTMENT,
            sprintf(sprintf("编辑部门【%s】", $department->name)),
            auth()->id()));
        return send_data(new DepartmentResource($department));
    }

    public function destroy(Department $department)
    {
        $descendantIds = Department::descendantsAndSelf($department->id)->pluck('id');
        if(User::whereIn('department_id', $descendantIds)->exists()){
            return error("单位或其下级部门已关联人员，请先解除关联", Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $department->delete();
        event(new OperationDone(OperationLog::DEPARTMENT,
            sprintf(sprintf("删除部门")),
            auth()->id()));
        return no_content();
    }

    public function multiDestroy()
    {
        $this->validate(request(), [
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'exists:departments,id']
        ],[],[
            'ids' => '单位id',
            'ids.*' => '单位id'
        ]);

        $departmentNamesHasUser = [];
        foreach (request('ids') as $id){
            $department = Department::find($id);
            $descendantIds = Department::descendantsAndSelf($id)->pluck('id');
            if(User::whereIn('department_id', $descendantIds)->exists()){
                array_push($departmentNamesHasUser, $department->name);
                continue;
            }
            $department->delete();
        }
        event(new OperationDone(OperationLog::DEPARTMENT,
            sprintf(sprintf("批量删除部门")),
            auth()->id()));
        return $departmentNamesHasUser
            ? error(implode(',', $departmentNamesHasUser) . "或其下级部门已关联人员，请先解除关联", Response::HTTP_UNPROCESSABLE_ENTITY)
            : no_content();

    }
}
