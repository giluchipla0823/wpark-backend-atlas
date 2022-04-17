<?php

namespace App\Http\Resources\Parking;

use App\Helpers\AppHelper;
use App\Http\Resources\Area\AreaResource;
use App\Http\Resources\Row\RowResource;
use App\Models\Parking;
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
        $fill = $this->rows->sum('fill');
        $fillPercentage = round(($fill / $this->capacity) * 100, 2);

        return [
            'id' => $this->id,
            'name' => $this->name,
            'area' => new AreaResource($this->area),
            'parking_type' => new ParkingTypeResource($this->parkingType),
            'start_row' => $this->start_row,
            'end_row' => $this->end_row,
            'capacity' => $this->capacity,
            'fill' => $fill,
            'fill_percentage' => $fillPercentage,
            'fill_type' => AppHelper::getFillTypeToParkingOrRow($fillPercentage),
            'capacitymm' => $this->capacitymm,
            'full' => $this->full,
            'active' => $this->active,
            'comments' => $this->comments,
        ];
    }
}
