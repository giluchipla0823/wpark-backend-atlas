<?php

namespace App\Http\Resources\Route;

use App\Http\Resources\Compound\CompoundResource;
use Illuminate\Http\Resources\Json\JsonResource;

class RouteResource extends JsonResource
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
            'cdm_code' => $this->cdm_code,
            'origin_compound' => new CompoundResource($this->originCompound),
            'destination_compound' => new CompoundResource($this->destinationCompound),
            'comments' => $this->comment
        ];
    }
}
