<?php

namespace App\Listeners;

use App\Events\OperationDone;
use App\Models\OperationLog;

class LogRecord
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param object $event
     * @return void
     */
    public function handle(OperationDone $event)
    {
        OperationLog::create([
            'module' => $event->module,
            'content' => $event->content,
            'user_id' => $event->userId ?? 0,
            'operated_ip' => request()->ip(),
            'operated_at' => now()
        ]);
    }
}
