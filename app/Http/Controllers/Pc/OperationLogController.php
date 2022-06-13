<?php

namespace App\Http\Controllers\Pc;

use App\Http\Controllers\Controller;
use App\Http\Resources\Pc\OperationLogResource;
use App\Models\OperationLog;

class OperationLogController extends Controller
{
    public function index()
    {
        return OperationLogResource::collection(OperationLog::whenName(request('name'))
            ->whenOperatedAtFrom(request('operated_at_from'))
            ->whenOperatedAtTo(request('operated_at_to'))
            ->whenModule(request('module'))
            ->with('user:id,name,real_name')
            ->latest('id')
            ->paginate(\request('pageSize', 10))
        );
    }
}
