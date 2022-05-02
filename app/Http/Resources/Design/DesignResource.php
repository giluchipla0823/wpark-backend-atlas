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
        return [
            'id' => $this->id,
            'name' => $this->name,
            'short_name' => $this->short_name,
            'code' => $this->code,
            'brand' => new BrandResource($this->brand),
            'lenght' => $this->lenght,
            'width' => $this->width,
            'height' => $this->height,
            'wreight' => $this->wreight,
            'description' => $this->description,
            'hybrid' => $this->hybrid,
            'manufacturing' => $this->manufacturing,
            //'svg' => $this->svg
        ];
    }
}
