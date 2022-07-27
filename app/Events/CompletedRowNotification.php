<?php

namespace App\Events;

use App\Models\Row;
use App\Models\User;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class CompletedRowNotification
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Row
     */
    public $row;

    /**
     * @var User
     */
    public $sendUser;

    /**
     * @var User|null
     */
    public $recipientUser;

    /**
     * @var array
     */
    public $params;

    /**
     * Create a new event instance.
     *
     * @param User $sendUser
     * @param Row $row
     * @param User|null $recipientUser
     */
    public function __construct(User $sendUser, Row $row, User $recipientUser = null)
    {
        $params = [
            'title' => 'Fila completada',
            'message' => 'Se ha completado la fila ' . $row->parking->name . '.' . $row->row_number,
            'item' => [
                'id' => $row->id,
                'name' => $row->parking->name . '.' . $row->row_number
            ]
        ];

        $this->row = $row;
        $this->sendUser = $sendUser;
        $this->recipientUser = $recipientUser;
        $this->params = $params;
    }

}
