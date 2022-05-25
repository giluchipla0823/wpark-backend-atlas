<?php

namespace App\Http\Resources\Brand;

use App\Http\Resources\Compound\CompoundResource;
use Illuminate\Http\Resources\Json\JsonResource;

class BrandResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $relationships = array_keys($this->resource->getRelations());

        $response = [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
        ];

        if (in_array('compound', $relationships)) {
            $response['compound'] = new CompoundResource($this->compound);
        }

        return $response;
    }
}
