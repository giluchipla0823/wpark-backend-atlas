<?php

namespace App\Http\Resources\Row;

use App\Http\Resources\Color\ColorResource;
use App\Http\Resources\Design\DesignResource;
use App\Http\Resources\Rule\RuleResource;
use App\Http\Resources\Slot\SlotResource;
use App\Models\Rule;
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
            "color" => (new ColorResource($this->color))->toArray($request),
            "last_rule" => $this->includeRule($this->lastRule),
            "shipping_rule" => $this->includeRule($this->shippingRule),
            "slot" => $this->includeSlot()
        ];
    }

    /**
     * @return SlotResource|null
     */
    private function includeSlot(): ?SlotResource
    {
        return $this->lastConfirmedMovement
                    ? new SlotResource($this->lastConfirmedMovement->destinationPosition)
                    : null;
    }

    /**
     * @param Rule|null $rule
     * @return array|null
     */
    private function includeRule(?Rule $rule): ?array
    {
        if (!$rule) {
            return null;
        }

        return [
            "id" => $rule->id,
            "name" => $rule->name
        ];
    }
}
