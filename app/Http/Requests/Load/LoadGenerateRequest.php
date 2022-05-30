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
            'transport_exit_id' => "required|integer|max:20|min:1|exists:transports,id",
            'transport_identifier' => 'required|string|min:1|max:50',
            "carrier_id" => "required|integer|exists:carriers,id",
            "license_plate" => "required|String",
            "compound_id" => "required|integer|exists:compounds,id",
            "vins" => "required|array",
            "vins.*" => "required|string|min:17|max:17"
        ];
    }
}
