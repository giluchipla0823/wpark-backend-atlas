<?php

namespace App\Http\Resources\Hold;

use App\Http\Resources\Condition\ConditionResource;
use Illuminate\Http\Resources\Json\JsonResource;

class HoldResource extends JsonResource
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
            'priority' => $this->priority,
            'role' => $this->role()->get(),
            'active' => $this->active,
            'count' => $this->count,
            'conditions' => ConditionResource::collection($this->conditions)
        ];
    }
}
