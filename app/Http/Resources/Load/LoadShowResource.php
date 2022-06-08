<?php

namespace App\Http\Resources\Load;

use App\Http\Resources\Carrier\CarrierResource;
use App\Http\Resources\Compound\CompoundResource;
use App\Http\Resources\Transport\TransportResource;
use Illuminate\Http\Resources\Json\JsonResource;

class LoadShowResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param $request
     * @return array
     */
    public function toArray($request): array
    {
        $relationships = array_keys($this->resource->getRelations());

        $response = [
            'id' => $this->id,
            'transport_identifier' => $this->transport_identifier,
            'license_plate' => $this->license_plate,
            'trailer_license_plate' => $this->trailer_license_plate,
            'ready' => $this->ready,
            'processed' => $this->processed
        ];

        if (in_array('carrier', $relationships)) {
            $response['carrier'] = new CarrierResource($this->carrier);
        }

        if (in_array('transport', $relationships)) {
            $response['transport'] = new TransportResource($this->transport);
        }

        if (in_array('compound', $relationships)) {
            $response['compound'] = new CompoundResource($this->compound);
        }

        return $response;

    }
}
