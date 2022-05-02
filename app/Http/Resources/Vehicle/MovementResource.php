<?php

namespace App\Http\Resources\Vehicle;

use JsonSerializable;
use App\Models\Slot;
use App\Models\Parking;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\JsonResource;

class MovementResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request)
    {
        $response = [
            'prev_position' => $this->includeOriginValues(),
            'current_position' => $this->includeDestinationValues()
        ];

        if ($this->confirmed == 0) {
            $response = [
                'prev_position' => null,
                'current_position' => $this->includeOriginValues()
            ];
        }

        return $response;
    }

    /**
     * @return Collection
     */
    private function includeOriginValues(): Collection
    {
        $prevPosition = null;
        $prevSlot = null;

        switch ($this->origin_position_type) {

            case Slot::class:
                $slot = Slot::find($this->origin_position_id);
                $row = $slot->row;
                $parking = $row->parking;

                $prevPosition = $parking->name . '.' . $row->row_number;
                $prevSlot = $slot->id;
                break;

            case Parking::class:
                $parking = Parking::find($this->origin_position_id);

                $prevPosition = $parking->name;
                break;
        }

        $collection = new Collection();
        $collection->push(['position' => $prevPosition, 'slot' => $prevSlot]);
        return $collection;
    }

    /**
     * @return Collection
     */
    private function includeDestinationValues(): Collection
    {
        $nextPosition = null;
        $nextSlot = null;

        switch ($this->destination_position_type) {

            case Slot::class:
                $slot = Slot::find($this->destination_position_id);
                $row = $slot->row;
                $parking = $row->parking;

                $nextPosition = $parking->name . '.' . $row->row_number;
                $nextSlot = $slot->id;
                break;

            case Parking::class:
                $parking = Parking::find($this->destination_position_id);

                $nextPosition = $parking->name;
                $nextSlot = '';
                break;
        }

        $collection = new Collection();
        $collection->push(['position' => $nextPosition, 'slot' => $nextSlot]);
        return $collection;
    }
}
