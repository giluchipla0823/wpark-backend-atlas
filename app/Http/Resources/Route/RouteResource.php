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
        $relationships = array_keys($this->resource->getRelations());

        $response = [
            "id" => $this->id,
            "name" => $this->name,
            "cdm_code" => $this->cdm_code,
            "route_type" => $this->route_type()->get(),
            'comments' => $this->comment,
            "default" => (bool) $this->default
        ];

        if (in_array('carrier', $relationships)) {
            $response['carrier'] = new CarrierResource($this->carrier);
        }

        if (in_array('destinationCode', $relationships)) {
            $response['destination_code'] = new DestinationCodeResource($this->destinationCode);
        }

        if (in_array('originCompound', $relationships)) {
            $response['origin_compound'] = new CompoundResource($this->originCompound);
        }

        if (in_array('destinationCompound', $relationships)) {
            $response['destination_compound'] = new CompoundResource($this->destinationCompound);
        }

        return $response;
    }
}
