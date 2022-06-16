<?php

namespace App\Http\Resources\Row;

use App\Helpers\AppHelper;
use App\Http\Resources\Block\BlockResource;
use App\Http\Resources\Brand\BrandResource;
use App\Http\Resources\Color\ColorResource;
use App\Http\Resources\Design\DesignResource;
use App\Http\Resources\Parking\ParkingResource;
use App\Http\Resources\Slot\SlotResource;
use Illuminate\Http\Resources\Json\JsonResource;

class RowEspigaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        return [
            'id' => $this->id,
            'row_number' => $this->row_number,
            'row_name' => $this->row_name,
            'parking' => $this->getParking(),
            // 'block' => $this->getBlock(),
            'category' => $this->category,
            'capacity' => $this->capacity,
            'fill' => $this->fill,
            'capacitymm' => $this->capacitymm,
            'fillmm' => $this->fillmm,
            'full' => $this->full,
            'alt_qr' => $this->alt_qr,
            'comments' => $this->comments,
            'active' => $this->active,
            'slot' => $this->getSlot()

        ];
    }

    private function getParking()
    {
        $parking = $this->parking;
        return [
            'id' => $parking->id,
            'name' => $parking->name,
            'area_id' => $parking->area_id,
            'parking_type_id' => $parking->parking_type_id,
            'start_row' => $parking->start_row,
            'end_row' => $parking->end_row,
            'capacity' => $parking->capacity,
            'fill' => $parking->fill,
            'full' => $parking->full,
            'order' => $parking->order,
            'active' => $parking->active,
            'comments' => $parking->comments
        ];
    }

    private function getBlock()
    {
        $block = $this->block;
        return [
            'id' => $block->id,
            'name' => $block->name,
            'is_presorting' => $block->is_presorting,
            'presorting_default' => $block->presorting_default,
            'active' => $block->active
        ];
    }

    private function getSlot()
    {
       return $this->slots->map(function($item, $key){
            return [
                'id' => $item->id,
                'slot_number' => $item->slot_number,
                'capacity' => $item->capacity,
                'fill' => $item->fill,
                'capacitymm' => $item->capacitymm,
                'fillmm' => $item->fillmm,
                'comments' => $item->comments,
                'vehicle' => $item->destinationMovement ? $this->getVehicle($item->destinationMovement->vehicle) : null
                // 'movement' => $this->getDestinationMovement($item->destinationMovement)
            ];
        })->first();
    }

    private function getDestinationMovement($movement)
    {
        if($movement){
            return [
                'id' => $movement->id,
                'vehicle_id' => $movement->vehicle_id,
                'user_id' => $movement->user_id,
                'origin_position_type' => $movement->origin_position_type,
                'origin_position_id' => $movement->origin_position_id,
                'destination_position_type' => $movement->destination_position_type,
                'destination_position_id' => $movement->destination_position_id,
                'category' => $movement->category,
                'confirmed' => $movement->confirmed,
                'canceled' => $movement->canceled,
                'manual' => $movement->manual,
                'dt_start' => $movement->dt_start,
                'dt_end' => $movement->dt_end,
                'comments' => $movement->comments,
                'vehicle' => $this->getVehicle($movement->vehicle)
            ];
        }
            return null;
    }

    private function getVehicle($vehicle)
    {
        if($vehicle){
            return [
                'id' => $vehicle->id,
                'vin' => $vehicle->vin,
                'lvin' => $vehicle->lvin,
                'vin_short' => $vehicle->vin_short,
                "color" => new ColorResource($vehicle->color),
                "design" => new DesignResource($vehicle->design),
//                'design_id' => $vehicle->design_id,
//                'color_id' => $vehicle->color_id,
//                'destination_code_id' => $vehicle->destination_code_id,
//                'entry_transport_id' => $vehicle->entry_transport_id,
//                'load_id' => $vehicle->load_id,
//                'dealer_id' => $vehicle->dealer_id,
//                'eoc' => $vehicle->eoc,
//                'last_rule_id' => $vehicle->last_rule_id,
//                'shipping_rule_id' => $vehicle->shipping_rule_id,
//                'info' => $vehicle->info
            ];
        }
        return null;
    }
}
