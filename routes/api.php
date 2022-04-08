<?php

use App\Http\Controllers\Api\AuditController as ApiAuditController;
use App\Http\Controllers\Api\PassingLogController as ApiPassingLogController;
use App\Http\Controllers\Api\UserController as ApiUserController;
use App\Http\Controllers\Api\IdCardController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\Pc\AuditController;
use App\Http\Controllers\Pc\AuthorizationController;
use App\Http\Controllers\Pc\BlacklistController;
use App\Http\Controllers\Pc\DepartmentController;
use App\Http\Controllers\Pc\GateController;
use App\Http\Controllers\Pc\ImportController;
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

Route::prefix('pc')->group(function (){
    Route::post('authorizations', [AuthorizationController::class, 'login'])->name('authorizations.login');
    //文件上传
    Route::post('files', [FileController::class, 'store'])->name('files.store');
});

Route::prefix('pc')->middleware('auth:sanctum')->name('pc.')->group(function(){
    //获取当前用户登陆信息
    Route::get('me', [AuthorizationController::class, 'me'])->name('authorizations.me');

    //部门管理
    Route::delete('departments/multi-destroy', [DepartmentController::class, 'multiDestroy'])->name('departments.multiDestroy');
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
    Route::get('ways/select', [WayController::class, 'select'])->name('ways.select');
    Route::delete('ways', [WayController::class, 'destroy'])->name('ways.destroy');
    Route::apiResource('ways', WayController::class)->except('destroy');

    //系统设置-权限管理
    Route::apiResource('permissions', PermissionController::class);

    //系统设置-角色管理
    Route::get('roles/select', [RoleController::class, 'select'])->name('roles.select');
    Route::delete('roles', [RoleController::class, 'destroy'])->name('roles.destroy');
    Route::apiResource('roles', RoleController::class)->except(['destroy']);

    //系统设置-人员类型设置
    Route::get('user-types/select', [UserTypeController::class, 'select'])->name('user-types.select');
    Route::apiResource('user-types', UserTypeController::class);

    //人员管理
    Route::delete('users', [UserController::class, 'destroy'])->name('users.destroy');
    Route::patch('users/{user}/reset', [UserController::class, 'reset'])->name('users.reset');
    Route::apiResource('users', UserController::class);

    //系统设置-访客设置-访客类型设置
    Route::get('visitor-types/select', [VisitorTypeController::class, 'select'])->name('visitor-types.select');
    Route::apiResource('visitor-types', VisitorTypeController::class);

    //系统设置-访客设置
    Route::get('visitor-settings/select', [VisitorSettingController::class, 'select'])->name('visitor-settings.select');
    Route::apiResource('visitor-settings', VisitorSettingController::class);

    //访客管理
    Route::delete('visitors', [VisitorController::class, 'destroy'])->name('visitors.destroy');
    Route::apiResource('visitors', VisitorController::class)->except('destroy');

    //黑名单管理
    Route::apiResource('blacklists', BlacklistController::class);

    //规则管理
    Route::apiResource('rules', RuleController::class)->only(['update', 'index']);

    //临时访客审核
    Route::apiResource('audits', AuditController::class)->only(['index', 'update', 'destroy', 'show']);

    //导入部门
    Route::post('import/departments', [ImportController::class, 'department'])->name('import.departments');
});

//身份证号是否合法
Route::get('id-cards/valid', [IdCardController::class, 'valid'])->name('idCards.valid');

//临时审核
Route::apiResource('audit', ApiAuditController::class)->only(['index', 'store']);

//通行记录
Route::post('passing-log', [ApiPassingLogController::class, 'store'])->name('passing-log.store');

//人员列表
Route::get('users', [ApiUserController::class, 'index'])->name('users.index');


