<?php

namespace App\Http\Resources\Load;

use App\Http\Resources\Brand\BrandResource;
use App\Http\Resources\Carrier\CarrierResource;
use App\Http\Resources\Compound\CompoundResource;
use App\Http\Resources\Transport\TransportResource;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class LoadDatatablesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $carrier = new CarrierResource($this->carrier);
        $transport = new TransportResource($this->transport);
        $compound = new CompoundResource($this->compound);

        return [
            'id' => $this->id,
            'transport_identifier' => $this->transport_identifier,
            'license_plate' => $this->license_plate,
            'carrier_name' => $carrier->name,
            'transport_exit_name' => $transport->name,
            'compound_name' => (!is_null($compound)) ? $compound->name : null,
            'ready' => $this->ready,
            'processed' => $this->processed,
            'created_at' => Carbon::parse($this->created_at)->format('Y-m-d H:i:s'),
        ];
    }
}
