<?php

namespace App\Http\Controllers\Pc;

use App\Events\OperationDone;
use App\Http\Controllers\Controller;
use App\Models\Backup;
use App\Models\OperationLog;

class BackupController extends Controller
{
    public function destroy(Backup $backup)
    {
        $backup->delete();
        event(new OperationDone(OperationLog::BACKUP,
            sprintf("删除备份【%s】", $backup->name),
            auth()->id()
        ));
        return no_content();
    }
}
