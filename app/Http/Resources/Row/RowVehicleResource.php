<?php

namespace App\Http\Resources\Row;

use App\Http\Resources\Slot\SlotResource;
use App\Models\Slot;
use Illuminate\Http\Resources\Json\JsonResource;

class RowVehicleResource extends JsonResource
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
            "id" => $this->id,
            "vin" => $this->vin,
            "vin_short" => $this->vin_short,
            "eoc" => $this->eoc,
            "slot" => $this->getCurrentSlot()
        ];
    }

    /**
     * @return Slot|null
     */
    protected function getCurrentSlot():? Slot
    {
        if (!$this->lastMovement) {
            return null;
        }

        if ($this->lastMovement->destination_slot) {
            return $this->lastMovement->destination_slot;
        }

        return $this->lastMovement->origin_slot;
    }
}
