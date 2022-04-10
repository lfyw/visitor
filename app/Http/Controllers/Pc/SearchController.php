<?php

namespace App\Http\Controllers\Pc;

use App\Http\Controllers\Controller;
use App\Http\Resources\Pc\SearchResource;
use App\Models\Visitor;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __invoke()
    {
//        return SearchResource::collection(Visitor::whenName(\request(''))->latest('id')
//            ->paginate(\request('pageSize', 10))
//        );
    }
}
