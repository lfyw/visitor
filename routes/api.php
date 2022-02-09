<?php

use App\Http\Controllers\Pc\DepartmentController;
use App\Http\Controllers\Pc\GateController;
use App\Http\Controllers\Pc\PassagewayController;
use App\Http\Controllers\Pc\PermissionController;
use App\Http\Controllers\Pc\RoleController;
use App\Http\Controllers\Pc\VisitorTypeController;
use App\Http\Controllers\Pc\WayController;
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

Route::prefix('pc')->name('pc.')->group(function(){
    //部门管理
    Route::apiResource('departments', DepartmentController::class);

    //访客设置
    Route::apiResource('visitorTypes', VisitorTypeController::class);

    //闸机管理
    Route::get('gates/select', [GateController::class, 'select'])->name('gates.select');
    Route::delete('gates', [GateController::class, 'destroy'])->name('gates.destroy');
    Route::apiResource('gates', GateController::class)->except(['destroy']);

    //通道管理
    Route::get('passageways/select', [PassagewayController::class, 'select'])->name('passageways.select');
    Route::delete('passageways', [PassagewayController::class, 'destroy'])->name('passageways.destroy');
    Route::apiResource('passageways', PassagewayController::class)->except('destroy');

    //路线管理
    Route::delete('ways', [WayController::class, 'destroy'])->name('ways.destroy');
    Route::apiResource('ways', WayController::class)->except('destroy');

    //权限管理
    Route::apiResource('permissions', PermissionController::class);

    //角色管理
    Route::delete('roles', [RoleController::class, 'destroy'])->name('roles.destroy');
    Route::apiResource('roles', RoleController::class)->except(['destroy']);

    
});
