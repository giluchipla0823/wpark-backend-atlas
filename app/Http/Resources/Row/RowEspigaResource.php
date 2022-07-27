<?php

namespace App\Http\Resources\Row;

use App\Http\Resources\Color\ColorResource;
use App\Http\Resources\Design\DesignResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RowEspigaResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'row_number' => $this->row_number,
            'row_name' => $this->row_name,
            'parking' => $this->getParking(),
            'category' => $this->category,
            'capacity' => $this->capacity,
            'fill' => $this->fill,
            'capacitymm' => $this->capacitymm,
            'fillmm' => $this->fillmm,
            'full' => $this->full,
            'alt_qr' => $this->alt_qr,
            'comments' => $this->comments,
            'active' => $this->active,
            'slot' => $this->getSlot()
        ];
    }

    /**
     * @return array
     */
    private function getParking(): array
    {
        $parking = $this->parking;

        return [
            'id' => $parking->id,
            'name' => $parking->name,
            'area_id' => $parking->area_id,
            'parking_type_id' => $parking->parking_type_id,
            'start_row' => $parking->start_row,
            'end_row' => $parking->end_row,
            'capacity' => $parking->capacity,
            'fill' => $parking->fill,
            'full' => $parking->full,
            'order' => $parking->order,
            'active' => $parking->active,
            'comments' => $parking->comments
        ];
    }

    /**
     * @return mixed
     */
    private function getSlot()
    {
       return $this->slots->map(function($item) {

            $vehicle = null;

            if ($item->fill > 0 && $item->destinationMovement && $item->destinationMovement->confirmed === 1) {
                $vehicle = $this->getVehicle($item->destinationMovement->vehicle);
            }

            return [
                'id' => $item->id,
                'slot_number' => $item->slot_number,
                'capacity' => $item->capacity,
                'fill' => $item->fill,
                'capacitymm' => $item->capacitymm,
                'fillmm' => $item->fillmm,
                'comments' => $item->comments,
                'vehicle' => $vehicle
            ];
        })->first();
    }

    /**
     * @param $vehicle
     * @return array|null
     */
    private function getVehicle($vehicle): ?array
    {
        if($vehicle){
            return [
                'id' => $vehicle->id,
                'vin' => $vehicle->vin,
                'lvin' => $vehicle->lvin,
                'vin_short' => $vehicle->vin_short,
                "color" => new ColorResource($vehicle->color),
                "design" => new DesignResource($vehicle->design),
            ];
        }
        return null;
    }
}
