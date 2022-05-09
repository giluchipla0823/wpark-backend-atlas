<?php

namespace App\Http\Resources\DestinationCode;

use App\Http\Resources\Country\CountryResource;
use App\Http\Resources\Route\RouteResource;
use Illuminate\Http\Resources\Json\JsonResource;

class DestinationCodeResource extends JsonResource
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
            'name' => $this->name,
            'code' => $this->code,
            'country' => new CountryResource($this->country),
            'active' => $this->active
        ];
    }
}
