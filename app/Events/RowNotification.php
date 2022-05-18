<?php

namespace App\Events;

use App\Models\Row;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RowNotification
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $row;
    public $sendUser;
    public $recipientUser;
    public $params;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $sendUser, Row $row, $params, User $recipientUser = null,)
    {
        $this->row = $row;
        $this->sendUser = $sendUser;
        $this->recipientUser = $recipientUser;
        $this->params = $params;
    }

}
