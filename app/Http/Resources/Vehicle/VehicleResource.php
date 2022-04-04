<?php

namespace App\Http\Resources\Vehicle;

use App\Http\Resources\Color\ColorResource;
use App\Http\Resources\Country\CountryResource;
use App\Http\Resources\Design\DesignResource;
use App\Http\Resources\DestinationCode\DestinationCodeResource;
use App\Http\Resources\Vehicle\StageResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

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
        return [
            'id' => $this->id,
            'vin' => $this->vin,
            'vin_short' => $this->vin_short,
            'design' => new DesignResource($this->design),
            'color' => new ColorResource($this->color),
            'country' => new CountryResource($this->country),
            'destination_code' => new DestinationCodeResource($this->destinationCode),
            'eoc' => '2FUS K5UO385120 WPGUMK08952 5IG 73UD NVBF KE5R DB 5FABJD SOPI 235 BO E',
            'hybrid' => 1,
            'stages' => StageResource::collection($this->stages),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
