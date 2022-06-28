<?php

namespace App\Http\Resources\Movement;

use App\Helpers\JsonResourceHelper;
use App\Http\Resources\Brand\BrandResource;
use App\Http\Resources\Country\CountryResource;
use App\Http\Resources\Parking\ParkingResource;
use App\Http\Resources\Slot\SlotResource;
use App\Models\Movement;
use App\Models\Parking;
use App\Models\Slot;
use App\Models\Zone;
use Illuminate\Database\Eloquent\Model;
use JsonSerializable;
use Illuminate\Http\Request;
use App\Http\Resources\Color\ColorResource;
use App\Http\Resources\Design\DesignResource;
use App\Http\Resources\DestinationCode\DestinationCodeResource;
use App\Http\Resources\Rule\RuleResource;
use App\Http\Resources\State\StateResource;
use App\Http\Resources\Stage\StageResource;
use App\Http\Resources\Vehicle\MovementResource;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\JsonResource;

class MovementVehicleRecommendResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request)
    {
        // dd($this);
        return [
            'id' => $this->id,
            'vin' => $this->vin,
            'vin_short' => $this->vin_short,
            'eoc' => $this->eoc,
            "design" => (new DesignResource($this->design))->toArray($request),
            "brand" => new BrandResource($this->design->brand),
            "color" => new ColorResource($this->color),
            "country" => new CountryResource($this->destinationCode->country),
            'category' => $this->category,
            'info' => $this->info,
            "origin_position" => $this->includePositionType('originPosition'),
            "destination_position" => $this->includePositionType('destinationPosition'),
            "last_rule" => $this->lastRule ? [
                "id" => $this->lastRule->id,
                "name" => $this->lastRule->name,
            ] : null,
            "shipping_rule" => $this->shippingRule ? [
                "id" => $this->shippingRule->id,
                "name" => $this->shippingRule->name,
            ] : null,
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
            $model->load(['row']);
        }

        /* @var SlotResource|ParkingResource $resourceInstance */
        $resourceInstance = (new $resourceClass($model));

        $collection = collect($resourceInstance->jsonSerialize());

        $collection->put("type", $modelClass);

        if ($modelClass === Slot::class) {
            $collection->put("name", $model->row_name);

            if ($collection->has("row")) {
                $rowData = collect($collection->get("row")->jsonSerialize());

                $parking = $rowData['parking']->resource;

                $isPresortingZone = $parking->area->zone->id === Zone::PRESORTING;

                $row = $rowData->except(["parking", "block", "slots"]);

                $row->put('is_presorting_zone', $isPresortingZone);

                $collection->put("row", $row);
            }
        }

        return $collection->toArray();
    }
}
