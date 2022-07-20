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
        $carrier = new CarrierResource($this->carrier);
        $transport = new TransportResource($this->transport);
        $compound = new CompoundResource($this->compound);

        return [
            "id" => $this->id,
            "transport_identifier" => $this->transport_identifier,
            "license_plate" => $this->license_plate,
            "category" => $this->category,
            "carrier_name" => $carrier->name,
            "carrier_code" => $carrier->code,
            "transport_exit_name" => $transport->name,
            "compound_name" => $compound->name,
            "ready" => $this->ready,
            "processed" => $this->processed,
            "status" => $this->processed === 1 && $this->ready === 1 ? 'confirmed' : 'pending',
            "created_at" => Carbon::parse($this->created_at)->format("Y-m-d H:i:s"),
        ];
    }
}
