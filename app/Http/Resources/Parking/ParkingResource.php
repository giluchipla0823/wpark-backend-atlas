<?php

namespace App\Http\Resources\Parking;

use App\Helpers\AppHelper;
use App\Http\Resources\Area\AreaResource;
use App\Http\Resources\Row\RowResource;
use App\Models\Parking;
use Illuminate\Http\Resources\Json\JsonResource;

class ParkingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
//        $capacity = $this->capacity ?: 0;
//        $fill = $this->rows->sum("fill");
//        $fillPercentage = $capacity > 0 ? round(($fill / $capacity) * 100, 2) : 0;

        return [
            "id" => $this->id,
            "name" => $this->name,
            "lp_name" => $this->lp_name,
            "lp_code" => $this->lp_code,
            "area" => new AreaResource($this->area),
            "parking_type" => new ParkingTypeResource($this->parkingType),
            "start_row" => $this->start_row,
            "end_row" => $this->end_row,
            "capacity" => $this->capacity,
//            "fill" => $fill,
//            "fill_percentage" => $fillPercentage,
//            "fill_type" => AppHelper::getFillTypeToParkingOrRow($fillPercentage),
            "fill" => $this->fill_calculate,
            "fill_percentage" => $this->fill_percentage,
            "fill_type" => $this->fill_type,
            // "capacitymm" => $this->capacitymm,
            "full" => $this->full,
            "order" => $this->order,
            "active" => $this->active,
            "comments" => $this->comments,
        ];
    }
}
