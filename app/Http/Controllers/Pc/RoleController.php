<?php

namespace App\Http\Controllers\Pc;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pc\RoleRequest;
use App\Http\Resources\Pc\RoleResource;
use App\Models\Role;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    public function index()
    {
        return RoleResource::collection(Role::withCount('users')->with('permissions')->latest('id')->paginate(request('pageSize', 10)));
    }

    public function store(RoleRequest $roleRequest)
    {
        $role = DB::transaction(function() use ($roleRequest){
            $role = Role::create(['name' => $roleRequest->name]);
            $role->permissions()->attach($roleRequest->permission_ids);
            return $role;
        });
        return send_data(new RoleResource($role->load('permissions')));
    }

    public function show(Role $role)
    {
        return send_data(new RoleResource($role->load('permissions')));
    }

    public function update(Role $role, RoleRequest $roleRequest)
    {
        $role = DB::transaction(function() use ($roleRequest, $role){
            $role->fill(['name' => $roleRequest->name])->save();
            $role->permissions()->sync($roleRequest->permission_ids);
            return $role;
        });
        return send_data(new RoleResource($role->load('permissions')));
    }

    public function destroy(RoleRequest $roleRequest)
    {
        $roles = Role::findMany($roleRequest->ids);
        $invalidRoleIds = [];
        $invalidRoleNames = [];
        foreach($roles as $role){
            if($role->users->first()){
                array_push($invalidRoleIds, $role->id);
                array_push($invalidRoleNames, $role->name);
            }
        }

        Role::whereIn('id', $roleRequest->ids)->whereNotIn('id', $invalidRoleIds)->get()->each->delete();

        return $invalidRoleIds
            ? send_message(sprintf("角色 %s 已关联人员，请先解除对应关联", implode(',', $invalidRoleNames)), Response::HTTP_OK)
            : no_content();
    }

    public function select()
    {
        return send_data(RoleResource::collection(Role::all()));
    }
}
