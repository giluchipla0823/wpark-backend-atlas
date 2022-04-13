<?php

namespace App\Http\Resources\Parking;

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
        $relationships = array_keys($this->resource->getRelations());

        $response = [
            'id' => $this->id,
            'name' => $this->name,
            'area' => new AreaResource($this->area),
            'parking_type' => new ParkingTypeResource($this->parkingType),
            'start_row' => $this->start_row,
            'end_row' => $this->end_row,
            'capacity' => $this->capacity,
            'capacitymm' => $this->capacitymm,
            'full' => $this->full,
            'active' => $this->active,
            'comments' => $this->comments,
        ];

        if ($request->query->get('additional_data') === 'total-capacity') {
            $slotsAvailable = $this->rows->sum('capacity');
            $slotsFilled = $this->rows->sum('fill');
            $parkingFilledPercentage = round(($slotsFilled / $slotsAvailable) * 100, 2);

            $response['slots_available'] = $slotsAvailable;
            $response['slots_filled'] = $slotsFilled;
            $response['parking_filled_percentage'] = $parkingFilledPercentage;
            $response['parking_filled_type'] = Parking::getFilledCategory($parkingFilledPercentage);
        }

        return $response;
    }
}
