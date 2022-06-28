<?php

namespace App\Http\Resources\Movement;

use App\Http\Resources\Device\DeviceResource;
use App\Http\Resources\User\UserResource;
use App\Http\Resources\Vehicle\VehicleResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MovementShowResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'vehicle_id' => $this->vehicle_id,
            'user' => new UserResource($this->user),
            'device' => new DeviceResource($this->device),
            'origin_position_type' => $this->origin_position_type,
            'origin_position_id' => $this->origin_position_id,
            'destination_position_type' => $this->destination_position_type,
            'destination_position_id' => $this->destination_position_id,
            'category' => $this->category,
            'confirmed' => $this->confirmed,
            'canceled' => $this->canceled,
            'manual' => $this->manual,
            'dt_start' => $this->dt_start,
            'dt_end' => $this->dt_end,
            'comments' => $this->comments,
            'created_at' => $this->created_at
        ];
    }
}
