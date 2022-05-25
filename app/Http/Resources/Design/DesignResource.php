<?php

namespace App\Http\Resources\Design;

use App\Http\Resources\Brand\BrandResource;
use Illuminate\Http\Resources\Json\JsonResource;

class DesignResource extends JsonResource
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
            'short_name' => $this->short_name,
            'code' => $this->code,
            'lenght' => $this->lenght,
            'width' => $this->width,
            'height' => $this->height,
            'wreight' => $this->wreight,
            'description' => $this->description,
            'manufacturing' => $this->manufacturing,
            //'svg' => $this->svg
        ];

        if (in_array('brand', $relationships)) {
            $response['brand'] = new BrandResource($this->brand);
        }

        return $response;
    }
}
