<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        return UserResource::collection(User::whenRealName(request('real_name'))
            ->adminAlwaysBeHidden()
            ->with([
                'department.ancestors',
            ])
            ->get());
    }
}
