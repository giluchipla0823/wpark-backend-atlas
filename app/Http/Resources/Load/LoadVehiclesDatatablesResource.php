<?php

namespace App\Http\Resources\Load;

use App\Http\Resources\Brand\BrandResource;
use App\Http\Resources\Carrier\CarrierResource;
use App\Http\Resources\Color\ColorResource;
use App\Http\Resources\Compound\CompoundResource;
use App\Http\Resources\Condition\ModelConditionResource;
use App\Http\Resources\Design\DesignResource;
use App\Http\Resources\DestinationCode\DestinationCodeResource;
use App\Http\Resources\Transport\TransportResource;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class LoadVehiclesDatatablesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'vin' => $this->vin,
            'vin_short' => $this->vin_short,
            'model_name' => (new DesignResource($this->design))->name,
            'color_name' => (new ColorResource($this->color))->name,
            'destination_code' => (new DestinationCodeResource($this->destinationCode))->code,
        ];
    }
}
