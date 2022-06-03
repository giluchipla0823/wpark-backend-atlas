<?php

namespace App\Http\Resources\Load;

use App\Http\Resources\Brand\BrandResource;
use App\Http\Resources\Carrier\CarrierResource;
use App\Http\Resources\Compound\CompoundResource;
use App\Http\Resources\Transport\TransportResource;
use Illuminate\Http\Resources\Json\JsonResource;

class LoadResource extends JsonResource
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
            'transport_identifier' => $this->transport_identifier,
            'license_plate' => $this->license_plate,
            'trailer_license_plate' => $this->trailer_license_plate,
            'carrier' => new CarrierResource($this->carrier),
            'transport' =>  new TransportResource($this->transport),
            'compound' =>  new CompoundResource($this->compound),
            'ready' => $this->ready,
            'processed' => $this->processed
        ];

    }
}
