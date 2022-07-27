<?php

namespace App\Http\Resources\Compound;

use Illuminate\Http\Resources\Json\JsonResource;

class CompoundResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
        ];
    }
}
