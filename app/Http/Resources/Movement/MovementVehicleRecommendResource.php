<?php

namespace App\Http\Resources\Movement;

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
        return [
            'id' => $this->id,
            'vin' => $this->vin,
            'vin_short' => $this->vin_short,
            'eoc' => $this->eoc,
            'position' => $this->lastMovement ? new MovementResource($this->lastMovement) : $this->getPosition(),
            'category' => $this->category,
            'info' => $this->info,
        ];
    }

    /**
     * @return Array
     */
    public function getPosition(): Array
    {
        $position['prev_position'] = null;
        $position['current_position'] = [
            'position' => "CANOPY",
            'slot' => null
        ];

        return $position;
    }

}
