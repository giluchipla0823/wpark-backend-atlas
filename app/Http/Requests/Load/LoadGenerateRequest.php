<?php

namespace App\Http\Requests\Load;

use Illuminate\Foundation\Http\FormRequest;

class LoadGenerateRequest extends FormRequest
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
        return [
            "transport_exit_id" => "required|integer|max:20|min:1|exists:transports,id",
            "carrier_id" => "required|integer|exists:carriers,id",
            "license_plate" => "required|string|max:50",
            "trailer_license_plate" => "nullable|string|max:25",
            "compound_id" => "required|integer|exists:compounds,id",
            "vehicles" => "required|array",
            "vehicles.*.vin" => "required|string|size:17|exists:vehicles,vin",
            "vehicles.*.route_id" => [
                "required",
                "integer",
                "exists:routes,id"
            ],
        ];
    }
}
