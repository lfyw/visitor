<?php

namespace App\Observers;

use App\Models\Backup;
use Illuminate\Support\Facades\Storage;

class BackupObserver
{
    public function deleted(Backup $backup)
    {
        Storage::disk('public')->delete('backups/' . $backup->name);
    }
}
