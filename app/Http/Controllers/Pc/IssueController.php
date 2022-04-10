<?php

namespace App\Http\Controllers\Pc;

use App\Http\Controllers\Controller;
use App\Http\Resources\Pc\IssueResource;
use App\Models\Issue;
use Illuminate\Http\Request;

class IssueController extends Controller
{
    public function index()
    {
        return IssueResource::collection(Issue::with(['gate.passageways'])->latest('id')->paginate(\request('pageSize', 10)));
    }
}
