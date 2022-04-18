<?php

namespace App\Http\Resources\Rule;

use JsonSerializable;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Http\Resources\Block\BlockResource;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\RuleCondition\RuleConditionResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RuleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request)
    {
        $relationships = array_keys($this->resource->getRelations());

        $response = [
            'id' => $this->id,
            'name' => $this->name,
            'priority' => $this->priority,
            'countdown' => $this->countdown,
            'active' => $this->active,
        ];

        if (in_array('conditions', $relationships)) {
            $response['conditions'] = $this->includeConditions();
        }

        if (in_array('blocks', $relationships)) {
            $response['blocks'] = BlockResource::collection($this->blocks);
        }

        return $response;
    }

    /**
     * @return AnonymousResourceCollection
     */
    private function includeConditions(): AnonymousResourceCollection
    {
        /* @var Collection $conditions */
        $conditions = $this->conditions()->get();

        $results = collect([]);

        foreach ($conditions as $condition) {
            if ($index = array_search($condition->model, array_column($results->toArray(), 'model'))) {
                $results->get($index)->values->push($condition->pivot);
            } else {
                $condition->values = collect([$condition->pivot]);
                $results->push($condition);
            }
        }

        return RuleConditionResource::collection($results);
    }

}
