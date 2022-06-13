<?php

namespace App\Http\Controllers\Pc;

use App\Events\OperationDone;
use App\Http\Controllers\Controller;
use App\Http\Resources\Pc\BackupResource;
use App\Models\Backup;
use App\Models\OperationLog;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BackupController extends Controller
{
    public function index()
    {
        return BackupResource::collection(Backup::latest('id')
            ->name(request('name'))
            ->createdAtFrom(request('created_at_from'))
            ->createdAtTo(request('created_at_to'))
            ->paginate(request('pageSize', 10)));
    }

    public function store()
    {
        Artisan::call('backup:db 手动备份');
        event(new OperationDone(OperationLog::BACKUP,
            sprintf("手动备份数据"),
            auth()->id()
        ));
        return no_content();
    }

    public function destroy(Backup $backup)
    {
        $backup->delete();
        event(new OperationDone(OperationLog::BACKUP,
            sprintf("删除备份【%s】", $backup->name),
            auth()->id()
        ));
        return no_content();
    }

    public function download(Backup $backup)
    {
        $file = storage_path('app/public/backups/') . $backup->name;
        if (!file_exists($file)) {
            return send_message('文件不存在，请确认', Response::HTTP_NOT_FOUND);
        }
        event(new OperationDone(OperationLog::BACKUP,
            sprintf("下载备份数据【%s】", $backup->name),
            auth()->id()
        ));
        return send_data([
            'url' => Str::after(Storage::disk('public')->url('backups/' . $backup->name), config('app.url'))
        ], Response::HTTP_OK);
    }
}
