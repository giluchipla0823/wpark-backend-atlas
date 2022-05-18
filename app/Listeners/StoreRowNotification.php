<?php

namespace App\Listeners;

use App\Events\RowNotification;
use App\Models\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class StoreRowNotification
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
     * @param  \App\Events\RowNotification  $event
     * @return void
     */
    public function handle(RowNotification $event): void
    {
        $sender_id = $event->sendUser->id;
        $recipient_id = $event->recipientUser ? $event->recipientUser->id : null;
        $resourceable_type = $event->row ? get_class($event->row) : null;
        $resourceable_id = $event->row ? $event->row->id : null;
        $data = json_encode($event->params);

        Notification::create([
            'sender_id' => $sender_id,
            'recipient_id' => $recipient_id,
            'type' => get_class($event),
            'resourceable_type' => $resourceable_type,
            'resourceable_id' => $resourceable_id,
            'reference_code' => Notification::ROW_COMPLETED,
            'data' => $data
        ]);
    }
}
