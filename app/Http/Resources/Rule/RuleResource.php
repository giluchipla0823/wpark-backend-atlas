<?php

namespace App\Http\Resources\Rule;

use JsonSerializable;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Http\Resources\Block\BlockResource;
use App\Http\Resources\Carrier\CarrierResource;
use App\Http\Resources\Parking\ParkingResource;
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
            'is_group' => $this->is_group,
            'final_position' => $this->final_position,
            'predefined_zone' => $this->parking ? new ParkingResource($this->parking) : null,
            'carrier' => $this->carrier ? new CarrierResource($this->carrier) : null,
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

        // dd($conditions->toArray());

        $results = collect([]);

        foreach ($conditions as $condition) {

            $index = $results->search(function($item) use ($condition) {
                return $item->model === $condition->model;
            });

            // $index = array_search($condition->model, array_column($results->toArray(), 'model'));

            if ($index !== false) {
                $results->get($index)->values->push($condition->pivot);
            } else {
                $condition->values = collect([$condition->pivot]);
                $results->push($condition);
            }
        }

        return RuleConditionResource::collection($results);
    }

}
