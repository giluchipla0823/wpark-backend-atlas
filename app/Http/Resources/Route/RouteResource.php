<?php

namespace App\Http\Resources\Route;

use App\Http\Resources\Carrier\CarrierResource;
use App\Http\Resources\Compound\CompoundResource;
use App\Http\Resources\DestinationCode\DestinationCodeResource;
use Illuminate\Http\Resources\Json\JsonResource;

class RouteResource extends JsonResource
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
            'name' => $this->name,
            'cdm_code' => $this->cdm_code,
            'route_type_id' => $this->route_type()->get(),
            'carrier_id' => new CarrierResource($this->carrier),
            'destination_code_id' => new DestinationCodeResource($this->destination_code),
            'origin_compound' => new CompoundResource($this->originCompound),
            'destination_compound' => new CompoundResource($this->destinationCompound),
            'comments' => $this->comment
        ];
    }
}
