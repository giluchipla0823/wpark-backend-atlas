<?php

namespace App\Http\Resources\Row;

use App\Http\Resources\Block\BlockResource;
use App\Http\Resources\Parking\ParkingResource;
use App\Http\Resources\Slot\SlotResource;
use Illuminate\Http\Resources\Json\JsonResource;

class RowResource extends JsonResource
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
            "row_number" => $this->row_number,
            "row_name" => $this->row_name,
            "lp_name" => $this->lp_name,
            "lp_code" => $this->lp_code,
            "category" => $this->category,
            "capacity" => $this->capacity,
            "fill" => $this->fill,
            "fill_percentage" => $this->fill_percentage,
            "fill_type" => $this->fill_type,
            "capacitymm" => $this->capacitymm,
            "fillmm" => $this->fillmm,
            "full" => $this->full,
            "alt_qr" => $this->alt_qr,
            "comments" => $this->comments,
            "active" => $this->active
        ];

        if (in_array("parking", $relationships)) {
            $response["parking"] = new ParkingResource($this->parking);
        }

        if (in_array("block", $relationships)) {
            $response["block"] = new BlockResource($this->block);
        }

        if (in_array("slots", $relationships)) {
            $response["slots"] = SlotResource::collection($this->slots);
        }

        return $response;
    }
}
