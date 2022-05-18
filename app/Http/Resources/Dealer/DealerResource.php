<?php

namespace App\Http\Resources\Dealer;

use Illuminate\Http\Resources\Json\JsonResource;

class DealerResource extends JsonResource
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
            'zip_code' => $this->zip_code,
            'city' => $this->city,
            'street' => $this->street,
            'country' => $this->country,
        ];
    }
}
