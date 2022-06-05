<?php

namespace App\Http\Resources\Movement;

use App\Http\Resources\Slot\SlotFilteredPositionResource;
use App\Models\Row;
use JsonSerializable;
use Illuminate\Http\Request;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\JsonResource;

class MovementFilteredPositionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request)
    {

        return [
            'id' => $this->id,
            'row_number' => $this->row_number,
            'row_name' => $this->row_name,
            'slots' => SlotFilteredPositionResource::collection($this->emptySlots),
            'type' => Row::class,
            'rule_id' => $this->rule ? $this->rule->id : null,
            'full' => $this->full,
            'active' => $this->active
        ];
    }
}
