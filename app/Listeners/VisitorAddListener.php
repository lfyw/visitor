<?php

namespace App\Listeners;

use App\Events\VisitorAudit;
use App\Supports\Sdks\VisitorSynchronization;

class VisitorAddListener
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
     * @param  object  $event
     * @return void
     */
    public function handle(VisitorAudit $event)
    {
        VisitorSynchronization::add($event->audit);
    }
}
