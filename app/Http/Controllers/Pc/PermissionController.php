<?php

namespace App\Http\Controllers\Pc;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pc\PermissionRequest;
use App\Http\Resources\Pc\PermissionResouce;
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
        return send_data(new PermissionResouce($permission));
    }

    public function show(Permission $permission)
    {
        return send_data(new PermissionResouce($permission));
    }

    public function update(Permission $permission, PermissionRequest $permissionRequest)
    {
        $permission->fill($permissionRequest->validated())->save();
        return send_data(new PermissionResouce($permission));
    }

    public function destroy(Permission $permission)
    {
        DB::transaction(function() use ($permission){
            $permission->getDescendants()->each(fn($descendant) => $descendant->roles()->detach());
            $permission->delete();
        });
        return no_content();
    }
}
