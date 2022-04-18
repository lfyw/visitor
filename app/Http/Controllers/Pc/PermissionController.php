<?php

namespace App\Http\Controllers\Pc;

use App\Events\OperationDone;
use App\Http\Controllers\Controller;
use App\Http\Requests\Pc\PermissionRequest;
use App\Http\Resources\Pc\PermissionResouce;
use App\Models\OperationLog;
use App\Models\Permission;
use DB;

class PermissionController extends Controller
{
    public function index()
    {
        return Permission::all()->toTree();
    }

    public function store(PermissionRequest $permissionRequest)
    {
        $permission = Permission::create($permissionRequest->validated());
        event(new OperationDone(OperationLog::PERMISSION,
            sprintf(sprintf("新增权限【%s】", $permissionRequest->name)),
            auth()->id()));
        return send_data(new PermissionResouce($permission));
    }

    public function show(Permission $permission)
    {
        return send_data(new PermissionResouce($permission));
    }

    public function update(Permission $permission, PermissionRequest $permissionRequest)
    {
        $permission->fill($permissionRequest->validated())->save();
        event(new OperationDone(OperationLog::PERMISSION,
            sprintf(sprintf("编辑权限【%s】", $permissionRequest->name)),
            auth()->id()));
        return send_data(new PermissionResouce($permission));
    }

    public function destroy(Permission $permission)
    {
        DB::transaction(function() use ($permission){
            $permission->getDescendants()->each(fn($descendant) => $descendant->roles()->detach());
            $permission->delete();
        });
        event(new OperationDone(OperationLog::PERMISSION,
            sprintf(sprintf("删除权限")),
            auth()->id()));
        return no_content();
    }
}
