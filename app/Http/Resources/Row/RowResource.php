<?php

namespace App\Http\Resources\Row;

use App\Http\Resources\Block\BlockResource;
use App\Http\Resources\Parking\ParkingResource;
use Illuminate\Http\Resources\Json\JsonResource;

class RowResource extends JsonResource
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
            'row_number' => $this->row_number,
            'parking' => new ParkingResource($this->parking),
            'block' => new BlockResource($this->block),
            'capacity' => $this->capacity,
            'fill' => $this->fill,
            'capacitymm' => $this->capacitymm,
            'fillmm' => $this->fillmm,
            'alt_qr' => $this->alt_qr,
            'comments' => $this->comments,
        ];
    }
}
