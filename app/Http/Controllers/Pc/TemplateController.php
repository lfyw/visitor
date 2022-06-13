<?php

namespace App\Http\Controllers\Pc;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pc\TemplateRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TemplateController extends Controller
{
    public function __invoke(TemplateRequest $request)
    {
        return send_data([
            'url' =>  Str::after(Storage::disk('template')->url($request->template. '.xlsx'), config('app.url'))
        ], Response::HTTP_OK);
    }
}
