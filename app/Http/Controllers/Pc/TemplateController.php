<?php

namespace App\Http\Controllers\Pc;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pc\TemplateRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class TemplateController extends Controller
{
    public function __invoke(TemplateRequest $request)
    {
        return send_data([
            'url' =>  Storage::disk('template')->url($request->template. '.xlsx')
        ], Response::HTTP_OK);
    }
}
