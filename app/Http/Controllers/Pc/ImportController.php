<?php

namespace App\Http\Controllers\Pc;

use App\Events\OperationDone;
use App\Exports\BlacklistsExport;
use App\Exports\DepartmentsExport;
use App\Exports\UsersExport;
use App\Exports\VisitorsExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Pc\ImportRequest;
use App\Imports\BlacklistsImport;
use App\Imports\DepartmentsImport;
use App\Imports\UsersImport;
use App\Imports\VisitorsImport;
use App\Models\OperationLog;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImportController extends Controller
{
    public function __invoke(ImportRequest $request)
    {
        return send_data($this->{$request->import}(), Response::HTTP_OK);
    }

    protected function blacklist()
    {
        $import = new BlacklistsImport();
        $import->import(request()->file('excel'));

        $hasError = (bool)($import->getErrorsCount() > 0);

        if ($hasError) {
            $export = new BlacklistsExport();
            $errorFilename = '黑名单错误数据' . time() . '.xlsx';
            $export->setErrors($import->getErrorsWithHeader())->store($errorFilename, 'error_xlsx');
            $errorXlsx = \Str::after(Storage::disk('error_xlsx')->url($errorFilename), config('app.url'));
        }

        event(new OperationDone(OperationLog::DEPARTMENT,
            sprintf(sprintf("批量导入黑名单")),
            auth()->id()));
        return [
            'total_count' => $import->getRowsCount(),
            'error_count' => $import->getErrorsCount(),
            'error_xlsx' => $errorXlsx ?? null
        ];
    }

    protected function department()
    {
        $import = new DepartmentsImport();
        $import->import(request()->file('excel'));

        $hasError = (bool)($import->getErrorsCount() > 0);

        if ($hasError) {
            $export = new DepartmentsExport();
            $errorFilename = '部门错误数据' . time() . '.xlsx';
            $export->setErrors($import->getErrorsWithHeader())->store($errorFilename, 'error_xlsx');
            $errorXlsx = Str::after(Storage::disk('error_xlsx')->url($errorFilename), config('app.url'));
        }

        event(new OperationDone(OperationLog::DEPARTMENT,
            sprintf(sprintf("批量导入部门")),
            auth()->id()));
        return [
            'total_count' => $import->getRowsCount(),
            'error_count' => $import->getErrorsCount(),
            'error_xlsx' => $errorXlsx ?? null
        ];
    }

    protected function user()
    {
        $import = new UsersImport();
        $import->import(request('excel'));

        $hasError = (bool)($import->getErrorsCount() > 0);

        if ($hasError) {
            $export = new UsersExport();
            $errorFilename = '人员错误数据' . time() . '.xlsx';
            $export->setErrors($import->getErrorsWithHeader())->store($errorFilename, 'error_xlsx');
            $errorXlsx = Str::after(Storage::disk('error_xlsx')->url($errorFilename), config('app.url'));
        }
        event(new OperationDone(OperationLog::DEPARTMENT,
            sprintf(sprintf("批量导入员工")),
            auth()->id()));
        return [
            'total_count' => $import->getRowsCount(),
            'error_count' => $import->getErrorsCount(),
            'error_xlsx' => $errorXlsx ?? null
        ];
    }

    protected function visitor()
    {
        $import = new VisitorsImport();
        $import->import(request('excel'));

        $hasError = (bool)($import->getErrorsCount() > 0);

        if ($hasError) {
            $export = new VisitorsExport();
            $errorFilename = '访客错误数据' . time() . '.xlsx';
            $export->setErrors($import->getErrorsWithHeader())->store($errorFilename, 'error_xlsx');
            $errorXlsx = Str::after(Storage::disk('error_xlsx')->url($errorFilename), config('app.url'));
        }
        event(new OperationDone(OperationLog::VISITOR,
            sprintf(sprintf("批量导入访客")),
            auth()->id()));
        return [
            'total_count' => $import->getRowsCount(),
            'error_count' => $import->getErrorsCount(),
            'error_xlsx' => $errorXlsx ?? null
        ];
    }
}
