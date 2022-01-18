<?php

use App\Http\Controllers\Pc\DepartmentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('pc')->group(function(){
    Route::delete('departments', [DepartmentController::class, 'destroy']);
    Route::apiResource('departments', DepartmentController::class)->except(['destroy']);
});
