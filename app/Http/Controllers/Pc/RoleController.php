<?php

namespace App\Http\Controllers\Pc;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pc\RoleRequest;
use App\Http\Resources\Pc\RoleResource;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    public function index()
    {
        return RoleResource::collection(Role::with('permissions')->latest('id')->paginate(request('pageSize', 10)));
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
        Role::findMany($roleRequest->ids)->each->delete();
        return no_content();
    }
}
