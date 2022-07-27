<?php

namespace App\Http\Resources\Load;

use Carbon\Carbon;
use App\Http\Resources\Carrier\CarrierResource;
use App\Http\Resources\Compound\CompoundResource;
use App\Http\Resources\Transport\TransportResource;
use Illuminate\Http\Resources\Json\JsonResource;

class LoadDatatablesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            "id" => $this->id,
            "transport_identifier" => $this->transport_identifier,
            "license_plate" => $this->license_plate,
            "category" => $this->category,
            "carrier" => new CarrierResource($this->carrier),
            "transport_exit" => new TransportResource($this->transport),
            "compound" => new CompoundResource($this->compound),
            "ready" => $this->ready,
            "processed" => $this->processed,
            "status" => $this->status,
            "created_at" => Carbon::parse($this->created_at)->format("Y-m-d H:i:s"),
        ];
    }
}
