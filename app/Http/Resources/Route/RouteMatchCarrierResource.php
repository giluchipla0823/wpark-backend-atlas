<?php

namespace App\Http\Resources\Route;

use App\Http\Resources\Carrier\CarrierResource;
use App\Http\Resources\Compound\CompoundResource;
use App\Http\Resources\DestinationCode\DestinationCodeResource;
use App\Http\Resources\ResourceType\RouteTypeResource;
use Illuminate\Http\Resources\Json\JsonResource;

class RouteMatchCarrierResource extends JsonResource
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
            'route_type' => new RouteTypeResource($this->route_type),
            'carrier_id' => $this->carrier_id,
            'destination_code_id' => $this->destination_code_id,
            'origin_compound_id' => $this->origin_compound_id,
            'destination_compound_id' => $this->destination_compound_id,
            'comments' => $this->comments
        ];
    }
}
