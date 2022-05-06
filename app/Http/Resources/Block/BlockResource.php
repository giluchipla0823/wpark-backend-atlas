<?php

namespace App\Http\Resources\Block;

use App\Http\Resources\Rule\RuleResource;
use Illuminate\Http\Resources\Json\JsonResource;

class BlockResource extends JsonResource
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
            'is_presorting' => $this->is_presorting,
            'active' => $this->active,
        ];

        if (in_array('rules', $relationships)) {
            $response['rules'] = RuleResource::collection($this->rules);
        }

        return $response;
    }
}
