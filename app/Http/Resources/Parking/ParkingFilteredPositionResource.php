<?php

namespace App\Http\Resources\Parking;

use App\Http\Resources\Area\AreaResource;
use App\Models\Parking;
use Illuminate\Http\Resources\Json\JsonResource;

class ParkingFilteredPositionResource extends JsonResource
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
            'area' => $this->area->name,
            'parking_type' => $this->parkingType->name,
            'type' => Parking::class,
            'full' => $this->full,
            'active' => $this->active,
        ];
    }
}
