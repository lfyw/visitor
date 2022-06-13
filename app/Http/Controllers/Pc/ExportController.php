<?php

namespace App\Http\Controllers\Pc;

use App\Exports\PassingLogsExport;
use App\Exports\UserExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Pc\ExportRequest;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ExportController extends Controller
{
    public function __invoke(ExportRequest $request)
    {
        return send_data($this->{Str::camel($request->export)}(), Response::HTTP_OK);
    }

    protected function passingLog()
    {
        $export = new PassingLogsExport();
        $filename = '综合查询-' . now()->format('YmdHis') . '.xlsx';
        $export->searcher([
            'id_card' => sm4encrypt(request('id_card')),
            'name' => request('name'),
            'type' => request('type'),
            'passageway_id' => request('passageway_id'),
            'gate_id' => request('gate_id'),
            'rule' => request('rule'),
            'passed_at_from' => request('passed_at_from'),
            'passed_at_to' => request('passed_at_to'),
            'ids' => request('ids')
        ])
            ->store($filename, 'output');

        return ['url' => Str::after(Storage::disk('output')->url($filename), config('app.url'))];
    }

    protected function user()
    {
        $export = new UserExport();
        $filename = '人员-' . now()->format('YmdHis') . '.xlsx';
        $export->searcher([
            'real_name' => request('real_name'),
            'role_id' => request('role_id'),
            'user_status' => request('user_status'),
            'department_id' => request('department_id'),
            'id_card' => sm4encrypt(request('id_card')),
            'phone_number' => request('phone_number'),
            'ids' => request('ids')
        ])
            ->store($filename, 'output');

        return ['url' => Str::after(Storage::disk('output')->url($filename), config('app.url'))];
    }
}
