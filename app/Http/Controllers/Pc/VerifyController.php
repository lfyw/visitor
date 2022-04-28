<?php

namespace App\Http\Controllers\Pc;

use App\Http\Controllers\Controller;

class VerifyController extends Controller
{
    public function __invoke()
    {
        return send_data(['verify' => true]);
    }
}
