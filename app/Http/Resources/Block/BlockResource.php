<?php

namespace App\Http\Resources\Block;

use App\Http\Resources\Row\RowResource;
use App\Http\Resources\Rule\RuleResource;
use Carbon\Carbon;
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
            "id" => $this->id,
            "name" => $this->name,
            "is_presorting" => (bool) $this->is_presorting,
            "presorting_default" => $this->is_presorting ? (bool) $this->presorting_default : null,
            "active" => (bool) $this->active,
            "created_at" => Carbon::parse($this->created_at)->format('Y-m-d H:i:s'),
            "updated_at" => $this->updated_at ? Carbon::parse($this->updated_at)->format('Y-m-d H:i:s') : null,
        ];

        if (in_array('rules', $relationships)) {
            $response['rules'] = RuleResource::collection($this->rules);
        }

        if (in_array('rows', $relationships)) {
            $response['rows'] = $this->rows;
        }

        return $response;
    }
}
