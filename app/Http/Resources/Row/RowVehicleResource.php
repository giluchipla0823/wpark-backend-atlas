<?php

namespace App\Http\Resources\Row;

use App\Http\Resources\Design\DesignResource;
use App\Http\Resources\Slot\SlotResource;
use Illuminate\Http\Resources\Json\JsonResource;

class RowVehicleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "vin" => $this->vin,
            "vin_short" => $this->vin_short,
            "eoc" => $this->eoc,
            "design" => (new DesignResource($this->design))->toArray($request),
            "slot" => $this->includeSlot()
        ];
    }

    /**
     * @return SlotResource|null
     */
    private function includeSlot(): ?SlotResource
    {
        return $this->lastMovement
                    ? new SlotResource($this->lastMovement->destinationPosition)
                    : null;
    }
}
