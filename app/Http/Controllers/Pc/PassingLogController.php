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
        return PassingLogResource::collection(PassingLog::filterByIdCard(\request('id_card'))->with(['gate.passageways'])->latest('id')->paginate(\request('pageSize', 10)));
    }
}
