<?php

namespace App\Http\Resources\Movement;

use Illuminate\Http\Resources\Json\JsonResource;

class MovementDatatablesResource extends JsonResource
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
            'id' => $this->id,
            'vehicle' => [
                'id' => $this->vehicle_id,
                'vin' => $this->vehicle_vin
            ],
            'origin_position' => $this->includeOriginPosition(),
            'destination_position' => $this->includeDestinationPosition(),
            'user' => [
                'id' => $this->user_id,
                'username' => $this->user_username
            ],
            'created_at' => $this->created_at,
            'status' => $this->status,
        ];
    }

    private function includeOriginPosition() {
        if (!$this->origin_position_name) {
            return null;
        }

        return [
            "id" => $this->origin_position_id,
            "name" => $this->origin_position_name,
            "type" => $this->origin_position_type,
            "parking" => [
                "id" => $this->origin_parking_id,
                "name" => $this->origin_parking_name,
            ],
            "row" => $this->origin_row_id ? [
                "id" => $this->origin_row_id,
                "row_number" => $this->origin_row_number,
            ] : null,
            "slot" => $this->origin_slot_id ? [
                "id" => $this->origin_slot_id,
                "slot_number" => $this->origin_slot_number,
            ] : null
        ];
    }

    private function includeDestinationPosition() {
        if (!$this->destination_position_name) {
            return null;
        }

        return [
            "id" => $this->destination_position_id,
            "name" => $this->destination_position_name,
            "type" => $this->destination_position_type,
            "parking" => [
                "id" => $this->destination_parking_id,
                "name" => $this->destination_parking_name,
            ],
            "row" => $this->destination_row_id ? [
                "id" => $this->destination_row_id,
                "row_number" => $this->destination_row_number,
            ] : null,
            "slot" => $this->destination_slot_id ? [
                "id" => $this->destination_slot_id,
                "slot_number" => $this->destination_slot_number,
            ] : null
        ];
    }
}
