<?php

namespace App\Http\Resources\Carrier;

use App\Http\Resources\Route\RouteMatchCarrierResource;
use Illuminate\Http\Resources\Json\JsonResource;

class CarrierMatchResource extends JsonResource
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
            'short_name' => $this->short_name,
            'code' => $this->code,
            'active' => $this->active,
            'routes' => RouteMatchCarrierResource::collection($this->routes)
        ];
    }
}
