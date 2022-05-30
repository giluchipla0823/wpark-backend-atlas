<?php

namespace App\Listeners;

use App\Events\CompletedRowNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendRowNotification
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
     * @param  \App\Events\CompletedRowNotification  $event
     * @return void
     */
    public function handle(CompletedRowNotification $event)
    {
        //
    }
}
