<?php

namespace App\Http\Controllers\Pc;

use App\Exports\DepartmentsExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Pc\ImportRequest;
use App\Imports\DepartmentsImport;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class ImportController extends Controller
{
    public function __invoke(ImportRequest $request)
    {
        return send_data($this->{$request->import}(), Response::HTTP_OK);
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
            $errorXlsx = Storage::disk('error_xlsx')->url($errorFilename);
        }
        return [
            'total_count' => $import->getRowsCount(),
            'error_count' => $import->getErrorsCount(),
            'error_xlsx' => $errorXlsx ?? null
        ];
    }
}
