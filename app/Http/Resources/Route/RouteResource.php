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
        // TODO: Añadir carrier y dealer en fase 2
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'origin_compound' => new CompoundResource($this->originCompound),
            'destination_compound' => new CompoundResource($this->destinationCompound),
            'comments' => $this->comment
        ];
    }
}
