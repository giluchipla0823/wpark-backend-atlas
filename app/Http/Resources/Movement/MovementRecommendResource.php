<?php

namespace App\Http\Resources\Movement;

use App\Helpers\ModelHelper;
use JsonSerializable;
use Illuminate\Http\Request;
use App\Models\Parking;
use App\Models\Slot;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\JsonResource;

class MovementRecommendResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request)
    {
        $type = get_class($this['position']);

        return [
            'position' => [
                "id" => $this['position']->id,
                "name" => $type === Parking::class ? $this['position']->name : $this['position']->row_name . "-". $this['position']->slot_number,
                "type" => $type
            ],
            'can_reload' => true,
            'parking' => [
                'id' => $type === Parking::class ? $this['position']->id : $this['position']->row->parking->id,
                'name' => $type === Parking::class ? $this['position']->name : $this['position']->row->parking->name
            ],
            'row' => $type === Parking::class ? null : [
                "id" => $this['position']->row->id,
                "row_number" => $this['position']->row->row_number,
                "qr_code" => $this['position']->row->alt_qr,
                "fill" => $this['position']->row->fill,
                "front_vehicle" => isset($this['vehicle']) ? new MovementVehicleRecommendResource($this['vehicle']) : null
            ],
            'slot' => $type === Parking::class ? null : [
                'id' => $this['position']->id,
                'slot_number' => $this['position']->slot_number
            ],
        ];
    }
}
