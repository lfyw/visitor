<?php

namespace App\Http\Controllers\Pc;

use App\Events\OperationDone;
use App\Http\Controllers\Controller;
use App\Http\Requests\Pc\AuthorizationRequest;
use App\Http\Resources\Pc\UserResource;
use App\Models\OperationLog;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class AuthorizationController extends Controller
{
    public function login(AuthorizationRequest $authorizationRequest)
    {
        $user = User::firstWhere('name', $authorizationRequest->name);
        if (!Hash::check($authorizationRequest->password, $user->password)){
            return error('密码错误', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $token = $user->createToken('sanctum');
        event(new OperationDone(OperationLog::LOGIN,
            sprintf("登录系统"),
            $user->id));
        return [
            'user' => new UserResource($user->loadMissing([
                'department.ancestors',
                'userType:id,name',
                'role:id,name',
                'ways'
            ])),
            'token' => $token->plainTextToken
        ];
    }

    public function me()
    {
        $user = auth()->user();
        return new UserResource($user->loadMissing([
            'department.ancestors',
            'userType:id,name',
            'role:id,name',
            'ways',
            'role.permissions:id,name,type,route,note'
        ]));
    }
}
