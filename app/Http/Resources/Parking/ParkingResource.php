<?php

namespace App\Http\Resources\Parking;

use App\Http\Resources\Area\AreaResource;
use App\Http\Resources\parking\parkingTypeResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ParkingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'area' => new AreaResource($this->area),
            'parking_type' => new parkingTypeResource($this->parkingType),
            'start_row' => $this->start_row,
            'end_row' => $this->end_row,
            'capacity' => $this->capacity,
            'capacitymm' => $this->capacitymm,
            'full' => $this->full,
            'active' => $this->active,
            'comments' => $this->comments,
        ];
    }
}
