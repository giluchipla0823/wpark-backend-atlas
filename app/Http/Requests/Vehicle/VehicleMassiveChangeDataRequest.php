<?php

namespace App\Http\Requests\Vehicle;

use Illuminate\Foundation\Http\FormRequest;

class VehicleMassiveChangeDataRequest extends FormRequest
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
            'vins' => 'required|array|min:1',
            'vins.*' => 'required|string',
            'option_change_data' => 'required|integer|in:1,2',
            'destination_code' => 'nullable|required_if:option_change_data,1|exists:destination_codes,id',
            'info' => 'required_if:option_change_data,2|max:100',
        ];
    }
}
