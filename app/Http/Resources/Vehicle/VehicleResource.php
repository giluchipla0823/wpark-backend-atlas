<?php

namespace App\Http\Resources\Vehicle;

use App\Http\Resources\Color\ColorResource;
use App\Http\Resources\Design\DesignResource;
use App\Http\Resources\DestinationCode\DestinationCodeResource;
use App\Http\Resources\State\StateResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Stage\StageResource;

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
        $relationships = array_keys($this->resource->getRelations());

        $response = [
            'id' => $this->id,
            'vin' => $this->vin,
            'vin_short' => $this->vin_short,
            'category' => $this->category,
            'eoc' => $this->eoc,
        ];

        if (in_array('design', $relationships)) {
            $response['design'] = new DesignResource($this->design);
        }

        if (in_array('color', $relationships)) {
            $response['color'] = new ColorResource($this->color);
        }

        if (in_array('destinationCode', $relationships)) {
            $response['destination_code'] = new DestinationCodeResource($this->destinationCode);
        }

        if (in_array('latestState', $relationships)) {
            $response['last_state'] = StateResource::collection($this->latestState)->collection->first();
        }

        if (in_array('latestStage', $relationships)) {
            $response['last_stage'] = StageResource::collection($this->latestStage)->collection->first();
        }

        if (in_array('lastConfirmedMovement', $relationships)) {
            $response['last_confirmed_movement'] = $this->lastConfirmedMovement;
        }

        return $response;
    }
}
