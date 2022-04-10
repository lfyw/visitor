<?php

namespace App\Http\Controllers\Pc;

use App\Http\Controllers\Controller;
use App\Http\Resources\Pc\PassingLogResource;
use App\Models\PassingLog;
use Illuminate\Http\Request;

class PassingLogController extends Controller
{
    public function index()
    {
        return PassingLogResource::collection(PassingLog::latest('id')->paginate(\request('pageSize', 10)));
    }
}
