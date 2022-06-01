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
        ];

        if (in_array('carrier', $relationships)) {
            $response['carrier'] = new CarrierResource($this->carrier);
        } else {
            $response['carrier_id'] = $this->carrier_id;
        }

        if (in_array('destinationCode', $relationships)) {
            $response['destination_code'] = new DestinationCodeResource($this->destinationCode);
        } else {
            $response['destination_code_id'] = $this->destination_code_id;
        }

        if (in_array('originCompound', $relationships)) {
            $response['origin_compound'] = new CompoundResource($this->originCompound);
        } else {
            $response['origin_compound_id'] = $this->origin_compound_id;
        }

        if (in_array('destinationCompound', $relationships)) {
            $response['destination_compound'] = new CompoundResource($this->destinationCompound);
        } else {
            $response['destination_compound_id'] = $this->destination_compound_id;
        }

        return array_merge($response, [
            'comments' => $this->comment,
            "default" => (bool) $this->default
        ]);
    }
}
