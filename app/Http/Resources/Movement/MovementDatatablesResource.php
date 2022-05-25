<?php

namespace App\Http\Resources\Movement;

use App\Helpers\ModelHelper;
use JsonSerializable;
use Illuminate\Http\Request;
use App\Http\Resources\Vehicle\VehicleResource;
use App\Http\Resources\User\UserResource;
use App\Models\Parking;
use App\Models\Slot;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\JsonResource;

class MovementDatatablesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'vehicle' => [
                'id' => $this->vehicle->id,
                'vin' => $this->vehicle->vin
            ],
            'origin_position' => $this->originResource(),
            'destination_position' => $this->destinationResource(),
            'user' => [
                'id' => $this->user->id,
                'username' => $this->user->username
            ],
        ];
    }

    /**
     * @return mixed
     */
    private function originResource(): mixed
    {
        if ($this->origin_position_type == Parking::class) {
            $origin = Parking::find($this->origin_position_id);
            if ($origin) {
                $name = $origin->name;
                $slot = null;
            }
        } else {
            $origin = Slot::find($this->origin_position_id);
            if ($origin) {
                $name = $origin->row_name;
                $slot = $origin->slot_number;
            }
        }

        return $origin ? ['id' => $origin->id, 'name' => $name, 'slot' => $slot] : null;
    }

    /**
     * @return mixed
     */
    private function destinationResource(): mixed
    {
        if ($this->destination_position_type == Parking::class) {
            $destination = Parking::find($this->destination_position_id);
            if ($destination) {
                $name = $destination->name;
                $slot = null;
            }
        } else {
            $destination = Slot::find($this->destination_position_id);
            if ($destination) {
                $name = $destination->row_name;
                $slot = $destination->slot_number;
            }
        }

        return $destination ? ['id' => $destination->id, 'name' => $name, 'slot' => $slot] : null;
    }
}
