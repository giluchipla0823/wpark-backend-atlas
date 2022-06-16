<?php

namespace App\Http\Resources\Row;

use App\Http\Resources\Block\BlockResource;
use App\Http\Resources\Movement\MovementVehicleRecommendResource;
use App\Http\Resources\Parking\ParkingResource;
use App\Http\Resources\Rule\RuleResource;
use App\Http\Resources\Slot\SlotResource;
use App\Models\Zone;
use Illuminate\Http\Resources\Json\JsonResource;

class RowShowResource extends JsonResource
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
            "row_number" => $this->row_number,
            "row_name" => $this->row_name,
            "lp_name" => $this->lp_name,
            "lp_code" => $this->lp_code,
            "rule" => new RuleResource($this->rule),
            "parking" => new ParkingResource($this->parking),
            "block" => new BlockResource($this->block),
            "category" => $this->category,
            "capacity" => $this->capacity,
            "fill" => $this->fill,
            "fill_percentage" => $this->fill_percentage,
            "fill_type" => $this->fill_type,
            "capacitymm" => $this->capacitymm,
            "fillmm" => $this->fillmm,
            "full" => $this->full,
            "alt_qr" => $this->alt_qr,
            "comments" => $this->comments,
            "active" => $this->active,
            "slots" => SlotResource::collection($this->slots),
            "is_presorting_zone" => $this->isPresortingZone(),
            "front_vehicle" => $this->includeFrontVehicle()
        ];
    }

    /**
     * @return bool
     */
    private function isPresortingZone(): bool
    {
        return $this->parking->area->zone->id === Zone::PRESORTING;
    }

    /**
     * @return MovementVehicleRecommendResource|null
     */
    private function includeFrontVehicle(): ?MovementVehicleRecommendResource
    {
        $slot = $this->slots->where("fill", 1)->last();

        return $slot && $slot->destinationMovement
                    ? new MovementVehicleRecommendResource($slot->destinationMovement->vehicle)
                    : null;
    }
}
