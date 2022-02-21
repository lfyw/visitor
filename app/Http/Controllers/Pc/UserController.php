<?php

namespace App\Http\Controllers\Pc;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pc\UserRequest;
use App\Http\Resources\Pc\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserController extends Controller
{

    public function index()
    {
        return UserResource::collection(User::whenRealName(request('real_name'))
            ->whenRoleId(request('role_id'))
            ->whenUserStatus(request('user_status'))
            ->with([
                'department.ancestors',
                'userType:id,name',
                'role:id,name',
            ])
            ->paginate(request('pageSize', 10))
        );
    }

    public function store(UserRequest $userRequest)
    {
        $user = DB::transaction(function() use ($userRequest){
            $validated = $userRequest->only(['name', 'real_name', 'department_id', 'user_type_id', 'role_id', 'user_status', 'duty', 'id_card', 'phone_number', 'issue_status']);
            $validated['password'] = bcrypt(Str::substr($validated['id_card'], -6, 6));
            $user = User::create($validated);
            $user->attachFiles($userRequest->face_picture_ids);
            $user->ways()->attach($userRequest->way_ids);
            return $user;
        });
        return send_data(new UserResource($user->load([
            'department.ancestors',
            'userType:id,name',
            'role:id,name',
            'ways'
        ])->loadFiles()));
    }

    public function show(User $user)
    {
        return send_data(new UserResource($user->load([
            'department.ancestors',
            'userType:id,name',
            'role:id,name',
            'ways'
        ])->loadFiles()));
    }

    public function update(UserRequest $userRequest, User $user)
    {
        $user = DB::transaction(function() use ($user, $userRequest){
            $validated = $userRequest->only(['name', 'real_name', 'department_id', 'user_type_id', 'role_id', 'user_status', 'duty', 'id_card', 'phone_number', 'issue_status']);
            $validated['password'] = bcrypt(Str::substr($validated['id_card'], -6, 6));
            $user->fill($validated)->save();
            $user->syncFiles($userRequest->face_picture_ids);
            $user->ways()->sync($userRequest->way_ids);
            return $user;
        });
        return send_data(new UserResource($user->load([
            'department.ancestors',
            'userType:id,name',
            'role:id,name',
            'ways'
        ])->loadFiles()));
    }

    public function destroy(UserRequest $userRequest)
    {
        User::findMany($userRequest->ids)->each(function(User $user){
            $user->detachFiles();
            $user->ways()->detach();
            $user->delete();
        });
        return no_content();
    }
}