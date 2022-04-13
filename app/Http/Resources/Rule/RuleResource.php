<?php

namespace App\Http\Resources\Rule;

use App\Http\Resources\Block\BlockResource;
use App\Http\Resources\Condition\ConditionResource;
use Illuminate\Http\Resources\Json\JsonResource;

class RuleResource extends JsonResource
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
            'priority' => $this->priority,
            'active' => $this->active,
            'conditions' => $this->conditions()->get(),
            //'blocks' => BlockResource::collection($this->blocks)
        ];
    }
}
