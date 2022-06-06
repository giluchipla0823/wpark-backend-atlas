<?php

namespace App\Http\Requests\Vehicle;

use App\Models\Parking;
use App\Models\Vehicle;
use Illuminate\Foundation\Http\FormRequest;

class VehicleManualStoreRequest extends FormRequest
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
        $positionTypes = implode(",",[Parking::class]);
        $createdFrom = implode(",",[Vehicle::CREATED_FROM_MOBILE, Vehicle::CREATED_FROM_WEB]);

        return [
            "vin" => "required|string|size:17|unique:vehicles",
            "lvin" => "required|string|size:17|unique:vehicles",
            "vin_short" => "required|string|size:7",
            "eoc" => "required|string|max:80",
            "design_id" => "required|integer|exists:designs,id",
            "color_id" => "required|integer|exists:colors,id",
            "destination_code_id" => "required|integer|exists:destination_codes,id",
            "entry_transport_id" => "required|integer|exists:transports,id",
            "info" => "nullable|max:100",
            "position" => "required|array|required_array_keys:type,id",
            "position.type" => "required|string|in:{$positionTypes}",
            "position.id" => [
                "required",
                "integer",
                "exists:parkings,id"
            ],
            "created_from" => "required|string|in:{$createdFrom}"
        ];
    }
}
