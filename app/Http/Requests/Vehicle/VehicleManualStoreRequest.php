<?php

namespace App\Http\Requests\Vehicle;

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
        $createdFrom = implode(",",[Vehicle::CREATED_FROM_MOBILE, Vehicle::CREATED_FROM_WEB]);

        return [
            "vin" => "required|string|size:17|unique:vehicles",
            "vin_short" => "required|string|size:7",
            "design_id" => "required|integer|exists:designs,id",
            "color_id" => "nullable|integer|exists:colors,id",
            "destination_code_id" => "required|integer|exists:destination_codes,id",
            "entry_transport_id" => "required|integer|exists:transports,id",
            "info" => "nullable|max:100",
            "parking_id" => "required|integer|exists:parkings,id",
            "created_from" => "required|string|in:{$createdFrom}"
        ];
    }
}
