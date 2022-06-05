<?php

namespace App\Http\Resources\Slot;

use App\Models\Slot;
use Illuminate\Http\Resources\Json\JsonResource;

class SlotFilteredPositionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        $response = [
            'id' => $this->id,
            'slot_number' => $this->slot_number,
            'row_name' => $this->row_name,
            'fill' => $this->fill,
            'type' => Slot::class
        ];

        return $response;
    }
}
