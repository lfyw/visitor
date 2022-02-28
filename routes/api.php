<?php

use App\Http\Controllers\Api\AuditController;
use App\Http\Controllers\Api\IdCardController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\Pc\BlacklistController;
use App\Http\Controllers\Pc\DepartmentController;
use App\Http\Controllers\Pc\GateController;
use App\Http\Controllers\Pc\PassagewayController;
use App\Http\Controllers\Pc\PermissionController;
use App\Http\Controllers\Pc\RoleController;
use App\Http\Controllers\Pc\RuleController;
use App\Http\Controllers\Pc\UserController;
use App\Http\Controllers\Pc\UserTypeController;
use App\Http\Controllers\Pc\VisitorController;
use App\Http\Controllers\Pc\VisitorSettingController;
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

    //系统设置-权限管理
    Route::apiResource('permissions', PermissionController::class);

    //系统设置-角色管理
    Route::get('roles/select', [RoleController::class, 'select'])->name('roles.select');
    Route::delete('roles', [RoleController::class, 'destroy'])->name('roles.destroy');
    Route::apiResource('roles', RoleController::class)->except(['destroy']);

    //系统设置-人员类型设置
    Route::apiResource('user-types', UserTypeController::class);

    //人员管理
    Route::delete('users', [UserController::class, 'destroy'])->name('users.destroy');
    Route::patch('users/{user}/reset', [UserController::class, 'reset'])->name('users.reset');
    Route::apiResource('users', UserController::class);

    //文件上传
    Route::post('files', [FileController::class, 'store'])->name('files.store');

    //系统设置-访客设置-访客类型设置
    Route::get('visitor-types/select', [VisitorTypeController::class, 'select'])->name('visitor-types.select');
    Route::apiResource('visitor-types', VisitorTypeController::class);

    //系统设置-访客设置
    Route::apiResource('visitor-settings', VisitorSettingController::class);

    //访客管理
    Route::delete('visitors', [VisitorController::class, 'destroy'])->name('visitors.destroy');
    Route::apiResource('visitors', VisitorController::class)->except('destroy');

    //黑名单管理
    Route::apiResource('blacklists', BlacklistController::class);

    //规则管理
    Route::apiResource('rules', RuleController::class)->only(['update', 'index']);
});

//身份证号是否合法
Route::get('id-cards/valid', [IdCardController::class, 'valid'])->name('idCards.valid');
//发起临时审核
Route::post('audit', [AuditController::class, 'store'])->name('audit.store');
