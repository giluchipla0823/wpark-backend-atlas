<?php

namespace App\Http\Resources\Vehicle;

use App\Helpers\JsonResourceHelper;
use App\Http\Resources\Hold\HoldResource;
use App\Http\Resources\Load\LoadShowResource;
use App\Http\Resources\Movement\MovementShowResource;
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
        if ($this->lastConfirmedMovement) {
            $this->lastConfirmedMovement->makeHidden('originPosition');
            $this->lastConfirmedMovement->makeHidden('destinationPosition');
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
            "last_state" => new StateResource($this->latestState->first()),
            "stages" => StageResource::collection($this->stages),
            "last_stage" => new StageResource($this->latestStage->first()),
            "stages_dates" => $this->stages->mapWithKeys(function ($item) {
                $name = str_replace(' ', '_', strtolower($item['name']));

                return [$name => $item['created_at']];
            }),
            "in_movement" => $this->inMovement(),
            "last_movement" => new MovementShowResource($this->lastMovement),
            "last_confirmed_movement" => new MovementShowResource($this->lastConfirmedMovement),
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
            "last_rule" => $this->lastRule ? [
                "id" => $this->lastRule->id,
                "name" => $this->lastRule->name,
            ] : null,
            "shipping_rule" => $this->shippingRule ? [
                "id" => $this->shippingRule->id,
                "name" => $this->shippingRule->name,
            ] : null,
            "load" => new LoadShowResource($this->loads),
            "last_recirculation" => new RecirculationResource($this->lastRecirculation),
            "recirculations" => $this->includeRecirculations(),
            "info" => $this->info
        ];
    }

    /**
     * @param string $relation
     * @return array|null
     */
    private function includePositionType(string $relation): ?array
    {
        /* @var Movement $lastConfirmedMovement */
        $lastConfirmedMovement = $this->lastConfirmedMovement;

        if (!$lastConfirmedMovement || !$lastConfirmedMovement->{$relation}) {
            return null;
        }

        /* @var Model $model */
        $model = $lastConfirmedMovement->{$relation};
        $modelClass = get_class($model);

        $resourceClass = $lastConfirmedMovement->resolvePositionResource($modelClass);

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
        $recirculations = $this->recirculations->sortByDesc('id');

        return RecirculationResource::collection($recirculations);

    }
}
