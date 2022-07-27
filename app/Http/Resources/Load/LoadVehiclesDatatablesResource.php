<?php

namespace App\Http\Resources\Load;

use App\Http\Resources\Color\ColorResource;
use App\Http\Resources\Route\RouteResource;
use App\Http\Resources\Design\DesignResource;
use App\Http\Resources\Rule\RuleResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\DestinationCode\DestinationCodeResource;

class LoadVehiclesDatatablesResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            "id" => $this->id,
            "vin" => $this->vin,
            "vin_short" => $this->vin_short,
            "design" => (new DesignResource($this->design))->toArray($request),
            "color" => (new ColorResource($this->color))->toArray($request),
            "destination_code" => (new DestinationCodeResource($this->destinationCode))->toArray($request),
            "route" => (new RouteResource($this->route))->toArray($request),
            "shipping_rule" => (new RuleResource($this->shippingRule))->toArray($request)
        ];
    }
}
