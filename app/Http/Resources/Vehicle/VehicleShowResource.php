<?php

namespace App\Http\Resources\Vehicle;

use App\Models\Slot;
use Illuminate\Database\Eloquent\Model;
use App\Http\Resources\Brand\BrandResource;
use App\Http\Resources\Color\ColorResource;
use App\Http\Resources\State\StateResource;
use App\Http\Resources\Design\DesignResource;
use App\Http\Resources\Country\CountryResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\DestinationCode\DestinationCodeResource;

class VehicleShowResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        if ($this->lastMovement) {
            $this->lastMovement->makeHidden('originPosition');
            $this->lastMovement->makeHidden('destinationPosition');
        }

        return [
            "id" => $this->id,
            "vin" => $this->vin,
            "vin_short" => $this->vin_short,
            "design" => (new DesignResource($this->design))->toArray($request),
            "brand" => new BrandResource($this->design->brand),
            "color" => new ColorResource($this->color),
            "destination_code" => (new DestinationCodeResource($this->destinationCode))->toArray($request),
            "country" => new CountryResource($this->destinationCode->country),
            "category" => $this->category,
            "eoc" => $this->eoc,
            "states" => StateResource::collection($this->states),
            "states_dates" => $this->states->mapWithKeys(function ($item) {
                $name = str_replace(' ', '_', strtolower($item['name']));

                return [$name => $item['created_at']];
            }),
            "last_state" => StateResource::collection($this->latestState)->collection->first(),
            "stages" => StageResource::collection($this->stages),
            "last_stage" => StageResource::collection($this->latestStage)->collection->first(),
            "stages_dates" => $this->stages->mapWithKeys(function ($item) {
                $name = str_replace(' ', '_', strtolower($item['name']));

                return [$name => $item['created_at']];
            }),
            "last_movement" => $this->lastMovement,
            "origin_position" => $this->includePositionType('originPosition'),
            "destination_position" => $this->includePositionType('destinationPosition'),
            "svg" => [
                "front" => route("designs-svg.default", ["filename" => "front.svg"]),
                "side" => route("designs-svg.default", ["filename" => "side.svg"]),
                "back" => route("designs-svg.default", ["filename" => "back.svg"]),
                "top" => route("designs-svg.default", ["filename" => "top.svg"]),
            ],
        ];
    }

    /**
     * @param string $relation
     * @return Model|null
     */
    private function includePositionType(string $relation): ?Model
    {
        $lastMovement = $this->lastMovement;

        if (!$lastMovement || !$lastMovement->{$relation}) {
            return null;
        }

        $modelClass = get_class($lastMovement->{$relation});

        $lastMovement->{$relation}->type = $modelClass;

        if ($modelClass === Slot::class) {
            $lastMovement->{$relation}->name = $lastMovement->{$relation}->row_name;
        }

        return $lastMovement->{$relation};
    }
}
