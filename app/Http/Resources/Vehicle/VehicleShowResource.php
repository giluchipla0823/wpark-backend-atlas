<?php

namespace App\Http\Resources\Vehicle;

use App\Helpers\JsonResourceHelper;
use App\Http\Resources\Hold\HoldResource;
use App\Http\Resources\Load\LoadShowResource;
use App\Http\Resources\Parking\ParkingResource;
use App\Http\Resources\Recirculation\RecirculationResource;
use App\Http\Resources\Slot\SlotResource;
use App\Models\Movement;
use App\Models\Slot;
use Illuminate\Database\Eloquent\Model;
use App\Http\Resources\Brand\BrandResource;
use App\Http\Resources\Color\ColorResource;
use App\Http\Resources\State\StateResource;
use App\Http\Resources\Design\DesignResource;
use App\Http\Resources\Country\CountryResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\DestinationCode\DestinationCodeResource;

class VehicleShowResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param $request
     * @return array
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
            "holds" => HoldResource::collection($this->holds),
            "svg" => [
                "front" => route("designs-svg.default", ["filename" => "front.svg"]),
                "side" => route("designs-svg.default", ["filename" => "side.svg"]),
                "back" => route("designs-svg.default", ["filename" => "back.svg"]),
                "top" => route("designs-svg.default", ["filename" => "top.svg"]),
            ],
            "last_rule_id" => $this->last_rule_id,
            "shipping_rule_id" => $this->shipping_rule_id,
            "load" => new LoadShowResource($this->loads),
            "last_recirculation" => new RecirculationResource($this->lastRecirculation),
            "recirculations" => $this->includeRecirculations()
        ];
    }

    /**
     * @param string $relation
     * @return array|null
     */
    private function includePositionType(string $relation): ?array
    {
        /* @var Movement $lastMovement */
        $lastMovement = $this->lastMovement;

        if (!$lastMovement || !$lastMovement->{$relation}) {
            return null;
        }

        /* @var Model $model */
        $model = $lastMovement->{$relation};
        $modelClass = get_class($model);

        $resourceClass = $lastMovement->resolvePositionResource($modelClass);

        if (!JsonResourceHelper::isInstance($resourceClass)) {
            return null;
        }

        if ($modelClass === Slot::class) {
            $model->load(['row', 'row']);
        }

        /* @var SlotResource|ParkingResource $resourceInstance */
        $resourceInstance = (new $resourceClass($model));

        $collection = collect($resourceInstance->jsonSerialize());

        $collection->put("type", $modelClass);

        if ($modelClass === Slot::class) {
            $collection->put("name", $model->row_name);

            if ($collection->has("row")) {
                $rowData = $collection->get("row")->jsonSerialize();
                $row = collect($rowData)->except(["block", "slots"]);

                $collection->put("row", $row);
            }
        }

        return $collection->toArray();
    }

    /**
     * @return AnonymousResourceCollection
     */
    private function includeRecirculations(): AnonymousResourceCollection
    {

        // $recirculations = $this->recirculations->sortByDesc('id')->unique("message")->sortBy('id')->values();
        $recirculations = $this->recirculations->sortByDesc('id');

        return RecirculationResource::collection($recirculations);

    }
}
