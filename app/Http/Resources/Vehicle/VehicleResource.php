<?php

namespace App\Http\Resources\Vehicle;

use App\Http\Resources\Color\ColorResource;
use App\Http\Resources\Country\CountryResource;
use App\Http\Resources\Design\DesignResource;
use App\Http\Resources\DestinationCode\DestinationCodeResource;
use App\Http\Resources\State\StateResource;
use App\Http\Resources\Vehicle\StageResource;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Resources\Json\JsonResource;

class VehicleResource extends JsonResource
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
            'vin' => $this->vin,
            'vin_short' => $this->vin_short,
            'design' => new DesignResource($this->design),
            'color' => new ColorResource($this->color),
            'destination_code' => new DestinationCodeResource($this->destinationCode),
            'category' => $this->category,
            'eoc' => $this->eoc,
            'last_stage' => StageResource::collection($this->latestStage)->collection->first(),
            'last_state' => StateResource::collection($this->latestState)->collection->first(),
            'last_movement' => $this->lastMovement
        ];
    }
}
