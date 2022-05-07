<?php

use App\Http\Controllers\Api\AuditController as ApiAuditController;
use App\Http\Controllers\Api\PassingLogController as ApiPassingLogController;
use App\Http\Controllers\Api\UserController as ApiUserController;
use App\Http\Controllers\Api\IdCardController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\Pc\AuditController;
use App\Http\Controllers\Pc\AuthorizationController;
use App\Http\Controllers\Pc\BackupController;
use App\Http\Controllers\Pc\BlacklistController;
use App\Http\Controllers\Pc\DepartmentController;
use App\Http\Controllers\Pc\ExportController;
use App\Http\Controllers\Pc\FacePictureController;
use App\Http\Controllers\Pc\GateController;
use App\Http\Controllers\Pc\BoardController;
use App\Http\Controllers\Pc\ImportController;
use App\Http\Controllers\Pc\IssueController;
use App\Http\Controllers\Pc\OperationLogController;
use App\Http\Controllers\Pc\PassagewayController;
use App\Http\Controllers\Pc\PassingLogController;
use App\Http\Controllers\Pc\PermissionController;
use App\Http\Controllers\Pc\RoleController;
use App\Http\Controllers\Pc\RuleController;
use App\Http\Controllers\Pc\TemplateController;
use App\Http\Controllers\Pc\UserController;
use App\Http\Controllers\Pc\UserTypeController;
use App\Http\Controllers\Pc\VisitorController;
use App\Http\Controllers\Pc\VisitorSettingController;
use App\Http\Controllers\Pc\VisitorTypeController;
use App\Http\Controllers\Pc\WarningController;
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

Route::prefix('pc')->group(function () {
    Route::post('authorizations', [AuthorizationController::class, 'login'])->name('authorizations.login');
    //文件上传
    Route::post('files', [FileController::class, 'store'])->name('files.store');
    //访客设置下拉框
    Route::get('visitor-settings/select', [VisitorSettingController::class, 'select'])->name('visitor-settings.select');
    //路线下拉框
    Route::get('ways/select', [WayController::class, 'select'])->name('ways.select');
});

Route::prefix('pc')->middleware('auth:sanctum')->name('pc.')->group(function () {
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
    Route::apiResource('visitor-settings', VisitorSettingController::class);

    //访客管理
    Route::delete('visitors', [VisitorController::class, 'destroy'])->name('visitors.destroy');
    Route::apiResource('visitors', VisitorController::class)->except('destroy');

    //黑名单管理
    Route::patch('visitors/blanklist/cancel', [BlacklistController::class, 'cancel'])->name('visitors.cancel');//取消拉黑
    Route::patch('visitors/blanklist/block', [BlacklistController::class, 'block'])->name('visitors.block');//批量拉黑
    Route::apiResource('blacklists', BlacklistController::class);

    //规则管理
    Route::apiResource('rules', RuleController::class)->only(['update', 'index']);

    //临时访客审核
    Route::apiResource('audits', AuditController::class)->only(['index', 'update', 'destroy', 'show']);

    //导入模板
    Route::get('templates', TemplateController::class)->name('templates.invoke');

    //导入
    Route::post('import', ImportController::class)->name('import.invoke');
    //导出
    Route::get('export', ExportController::class)->name('export.invoke');

    //下发记录
    Route::get('issue', [IssueController::class, 'index'])->name('issue.index');
    //重新下发
    Route::put('issue/{issue}', [IssueController::class, 'update'])->name('issue.update');
    //删除下发
    Route::delete('issue/delete-user', [IssueController::class, 'deleteUser'])->name('issue.deleteUser');
    //多个访客下发
    Route::post('issue/multi-visitor', [IssueController::class, 'multiVisitor'])->name('issue.multiVisitor');
    //多个人员下发
    Route::post('issue/multi-user', [IssueController::class, 'multiUser'])->name('issue.multiUser');
    //全部访客下发
    Route::post('issue/all-visitor', [IssueController::class, 'allVisitor'])->name('issue.allVisitor');
    //全部人员下发
    Route::post('issue/all-user', [IssueController::class, 'allUser'])->name('issue.allUser');

    //访问记录
    Route::get('passing-log', [PassingLogController::class, 'index'])->name('passing-log.index');

    //访问记录下拉框
    Route::get('passing-log/type-select', [PassingLogController::class, 'select'])->name('passing-log.select');

    //操作日志
    Route::get('operation-logs', [OperationLogController::class, 'index'])->name('operations-logs.index');

    //批量添加多个照片
    Route::post('face-pictures', [FacePictureController::class, 'store'])->name('face-pictures.store');

    //数据备份
    Route::get('backup/{backup}/download', [BackupController::class, 'download'])->name('backup.download');
    Route::apiResource('backup', BackupController::class)->only(['index', 'store', 'destroy']);

    //数据看板-进出人数/人次统计
    Route::get('board/passing-log-chart', [BoardController::class, 'passingLogChart'])->name('board.passingLogChart');
    //数据看板-通行时间统计
    Route::get('board/passing-time-chart', [BoardController::class, 'passingTimeChart'])->name('board.passingTimeChart');
    //数据看板-通道通行人次统计
    Route::get('board/passageway-passing-chart', [BoardController::class, 'passagewayPassingChart'])->name('board.passagewayPassingChart');
    //数据看板当前办公区人员统计
    Route::get('board', [BoardController::class, 'index'])->name('board.index');
    //数据看板-超时未出预警统计
    Route::get('board/warning', [BoardController::class, 'warning'])->name('board.warning');

    //预警列表
    Route::get('warnings', [WarningController::class, 'index'])->name('warnings.index');
    //预警处理
    Route::patch('warnings', [WarningController::class, 'update'])->name('warnings.update');
});

//身份证号是否合法
Route::get('id-cards/valid', [IdCardController::class, 'valid'])->name('idCards.valid');

//临时审核
Route::apiResource('audit', ApiAuditController::class)->only(['index', 'store']);

//通行记录
Route::post('passing-log', [ApiPassingLogController::class, 'store'])->name('passing-log.store');
//同行记录带截图参数
Route::post('passing-log-with-snapshot', [ApiPassingLogController::class, 'withSnapShot'])->name('passing-log.withSnapShot');

//人员列表
Route::get('users', [ApiUserController::class, 'index'])->name('users.index');

//根据身份证号查询历史申请临时访问记录
Route::get('audit/history', [ApiAuditController::class, 'history'])->name('audit.history');
