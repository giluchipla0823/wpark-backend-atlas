<?php

namespace App\Http\Resources\Vehicle;

use App\Http\Resources\Color\ColorResource;
use App\Http\Resources\Design\DesignResource;
use App\Http\Resources\DestinationCode\DestinationCodeResource;
use App\Http\Resources\Rule\RuleResource;
use App\Http\Resources\State\StateResource;
use App\Http\Resources\Stage\StageResource;
use App\Http\Resources\Vehicle\MovementResource;
use Illuminate\Http\Resources\Json\JsonResource;

class InfoVehicleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        $response = [
            'id' => $this->id,
            'vin' => $this->vin,
            'vin_short' => $this->vin_short,
            'position' => $this->lastConfirmedMovement ? new MovementResource($this->lastConfirmedMovement) : $this->getPosition(),
            'design' => new DesignResource($this->design),
            'color' => new ColorResource($this->color),
            'states' => StateResource::collection($this->states),
            'stages' => StageResource::collection($this->stages),
            'category' => $this->category,
            'destination_code' => new DestinationCodeResource($this->destinationCode),
            'category' => new RuleResource($this->shippingRule),
            'info' => $this->info,
        ];

        return $response;
    }

    /**
     * @return Array
     */
    public function getPosition(): Array
    {
        $position['prev_position'] = null;
        $position['current_position'] = [
            'position' => "CANOPY",
            'slot' => null
        ];

        return $position;
    }
}
