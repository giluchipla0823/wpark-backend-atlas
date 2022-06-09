<?php

namespace App\Http\Requests\Row;

use App\Models\Parking;
use App\Models\Slot;
use Illuminate\Foundation\Http\FormRequest;

class RowRellocateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $positionTypes = implode(",", [Slot::class, Parking::class]);

        return [
            "row_vehicles" => "required|array|min:1",
            "row_vehicles.*.vehicle_id" => "required|integer|exists:vehicles,id",
            "row_vehicles.*.origin_position" => "required|array|required_array_keys:id,type",
            "row_vehicles.*.origin_position.id" => [
                "required",
                "integer"
            ],
            "row_vehicles.*.origin_position.type" => "required|string|in:{$positionTypes}",
            "row_vehicles.*.destination_position" => "required|array|required_array_keys:id,type",
            "row_vehicles.*.destination_position.id" => [
                "required",
                "integer"
            ],
            "row_vehicles.*.destination_position.type" => "required|string|in:" . Slot::class,

            "buffer_vehicles" => "nullable|array",
            "buffer_vehicles.*.vehicle_id" => "required|integer|exists:vehicles,id",
            "buffer_vehicles.*.origin_position" => "required|array|required_array_keys:id,type",
            "buffer_vehicles.*.origin_position.id" => [
                "required",
                "integer"
            ],
            "buffer_vehicles.*.origin_position.type" => "required|string|in:" . Slot::class,
        ];
    }
}
