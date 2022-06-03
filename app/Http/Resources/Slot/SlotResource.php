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
        $relationships = array_keys($this->resource->getRelations());

        $response = [
            "id" => $this->id,
            "slot_number" => $this->slot_number,
            "lp_name" => $this->lp_name,
            "lp_code" => $this->lp_code,
            "row_name" => $this->row_name,
            "capacity" => $this->capacity,
            "fill" => $this->fill,
            "capacitymm" => $this->capacitymm,
            "fillmm" => $this->fillmm,
            "comments" => $this->comments,
        ];

        if (in_array("row", $relationships)) {
            $response["row"] = new RowResource($this->row);
        }

        return $response;
    }
}
