<?php

namespace App\Http\Resources\Slot;

use App\Http\Resources\Row\RowResource;
use Illuminate\Http\Resources\Json\JsonResource;

class SlotResource extends JsonResource
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
            'slot_number' => $this->slot_number,
            'row' => new RowResource($this->row),
            'capacity' => $this->capacity,
            'fill' => $this->fill,
            'capacitymm' => $this->capacitymm,
            'fillmm' => $this->fillmm,
            'comments' => $this->comments
        ];
    }
}
