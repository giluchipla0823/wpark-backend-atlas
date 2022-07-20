<?php

namespace App\Http\Resources\RuleCondition;

use App\Models\Design;
use App\Models\Stage;
use JsonSerializable;
use App\Models\Color;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use App\Models\DestinationCode;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\JsonResource;

class RuleConditionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param $request
     * @return array
     */
    public function toArray($request): array
    {
        $response = [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'model' => $this->model,
            'required' => $this->required
        ];

        if ($this->canAddConditionValues($request)) {
            $response['values'] = $this->includeValues();
        }

        return $response;
    }

    /**
     * @param Request $request
     * @return bool
     */
    private function canAddConditionValues(Request $request): bool
    {
        $extraIncludes = explode(',', $request->query->get('extra_includes'));

        return (
            in_array('conditions.values', $extraIncludes) &&
            $this->model &&
            $this->values instanceof Collection
        );
    }

    /**
     * @return Collection
     */
    private function includeValues(): Collection
    {
        $modelData = (new $this->model)->query()
            ->whereIn('id', array_column($this->values->toArray(), 'conditionable_id'))
            ->get();

        $field = null;

        switch ($this->model) {
            case Color::class:
            case Design::class:
            case Stage::class:
            case DestinationCode::class:
                $field = 'name';
                break;

            case Vehicle::class:
                $field = 'vin';
                break;
        }

        // dd($this->values->toArray());
        // dd($modelData->toArray());

        return $modelData->map(function($value) use ($field) {
            return [
                "id" => $value->id,
                "value" => $value->{$field},
            ];
        });

//        return $this->values->map(function($value) use ($modelData, $field) {
//            $model = $modelData->where('id', '=', $value->conditionable_id)
//                        ->first();
//
//            return [
//                'id' => $value->conditionable_id,
//                'value' => $model->{$field},
//            ];
//        });
    }
}
