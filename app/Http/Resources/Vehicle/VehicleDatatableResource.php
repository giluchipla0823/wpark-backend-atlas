<?php

namespace App\Http\Resources\Vehicle;

use Illuminate\Http\Resources\Json\JsonResource;

class VehicleDatatableResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "vin" => $this->vin,
            "vin_short" => $this->vin_short,
            "color" => [
                "id" => $this->color_id,
                "name" => $this->color_name,
            ],
            "design" => [
                "id" => $this->design_id,
                "name" => $this->design_name,
            ],
            "brand" => [
                "id" => $this->brand_id,
                "name" => $this->brand_name,
            ],
            "destination_code" => [
                "id" => $this->destination_code_id,
                "name" => $this->destination_code_name,
                "code" => $this->destination_code_codification,
            ],
            "country" => [
                "id" => $this->country_id,
                "code" => $this->country_code,
                "name" => $this->country_name,
            ],
            "dt_terminal" => $this->dt_terminal,
            "last_state" => $this->includeLastState(),
            "last_stage" => $this->includeLastStage(),
            "origin_position" => $this->includeOriginPosition(),
            "destination_position" => $this->includeDestinationPosition(),
            "shipping_rule" => $this->includeShippingRule(),
            "carrier" => $this->includeCarrier(),
            "transport_entry" => $this->includeTransportEntry(),
            "transport_exit" => $this->includeTransportExit(),
        ];
    }

    /**
     * @return array|null
     */
    private function includeLastState(): ?array
    {
        return $this->state_id ? [
            "id" => $this->state_id,
            "name" => $this->state_name,
            "description" => $this->state_description,
            "created_at" => $this->state_created_at,
        ] : null;
    }

    /**
     * @return array|null
     */
    private function includeLastStage(): ?array
    {
        return $this->stage_id ? [
            "id" => $this->stage_id,
            "name" => strtoupper($this->stage_name),
            "description" => $this->stage_description,
        ] : null;
    }

    private function includeOriginPosition(): ?array
    {
        return $this->origin_position_id ? [
            "id" => $this->origin_position_id,
            "name" => $this->origin_position_name,
            "slot" => $this->origin_position_slot,
            "type" => $this->origin_position_type,
        ] : null;
    }

    private function includeDestinationPosition(): ?array
    {
        return $this->destination_position_id ? [
            "id" => $this->destination_position_id,
            "name" => $this->destination_position_name,
            "slot" => $this->destination_position_slot,
            "type" => $this->destination_position_type,
        ] : null;
    }

    private function includeShippingRule(): ? array
    {
        return $this->shipping_rule_id ? [
            "id" => $this->shipping_rule_id,
            "name" => $this->shipping_rule_name
        ] : null;
    }

    private function includeCarrier(): ? array
    {
        return $this->carrier_id ? [
            "id" => $this->carrier_id,
            "name" => $this->carrier_name
        ] : null;
    }

    private function includeTransportEntry(): ? array
    {
        return $this->transport_entry_id ? [
            "id" => $this->transport_entry_id,
            "name" => $this->transport_entry_name
        ] : null;
    }

    private function includeTransportExit(): ? array
    {
        return $this->transport_exit_id ? [
            "id" => $this->transport_exit_id,
            "name" => $this->transport_exit_name
        ] : null;
    }


}
