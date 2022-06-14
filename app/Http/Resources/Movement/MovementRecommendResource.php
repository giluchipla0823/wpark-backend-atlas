<?php

namespace App\Http\Resources\Movement;

use App\Helpers\ModelHelper;
use App\Http\Resources\Parking\ParkingTypeResource;
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
     * @param $request
     * @return array
     */
    public function toArray($request)
    {
        $type = get_class($this["position"]);

        return [
            "id" => $this["movement"]->id,
            "position" => $this->includePosition($type),
            "can_reload" => true,
            "parking" => $this->includeParking($type),
            "row" => $this->includeRow($type),
            "slot" => $this->includeSlot($type)
        ];
    }

    /**
     * @param string $type
     * @return array
     */
    private function includePosition(string $type): array
    {
        return [
            "id" => $this["position"]->id,
            "name" => $type === Parking::class ? $this["position"]->name : $this["position"]->row_name . "-". $this["position"]->slot_number,
            "type" => $type
        ];
    }

    /**
     * @param string $type
     * @return array
     */
    private function includeParking(string $type): array
    {
        $parking = $type === Parking::class ? $this["position"] : $this["position"]->row->parking;

        return [
            "id" => $parking->id,
            "name" => $parking->name,
            "fill" => $parking->fill,
            "capacity" => $parking->capacity,
            "parking_type" => new ParkingTypeResource($parking->parkingType)
        ];
    }

    /**
     * @param string $type
     * @return array|null
     */
    private function includeRow(string $type): ?array
    {
        if ($type === Parking::class) {
            return null;
        }

        $row = $this["position"]->row;

        return [
            "id" => $row->id,
            "row_number" => $row->row_number,
            "qr_code" => $row->alt_qr,
            "fill" => $row->fill,
            "lp_code" => $row->lp_code,
            "lp_name" => $row->lp_name,
            "category" => $row->category,
            "row_name" => $row->row_name,
            "front_vehicle" => $this->includeFrontVehicle()
        ];
    }

    /**
     * @param string $type
     * @return array|null
     */
    private function includeSlot(string $type): ?array
    {
        return $type === Parking::class
            ? null
            : [
                "id" => $this["position"]->id,
                "slot_number" => $this["position"]->slot_number
            ];
    }

    /**
     * @return MovementVehicleRecommendResource|null
     */
    private function includeFrontVehicle(): ?MovementVehicleRecommendResource
    {
        return isset($this["vehicle"]) ? new MovementVehicleRecommendResource($this["vehicle"]) : null;
    }
}
