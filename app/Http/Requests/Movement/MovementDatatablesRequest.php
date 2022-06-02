<?php

namespace App\Http\Requests\Movement;

use Illuminate\Foundation\Http\FormRequest;

class MovementDatatablesRequest extends FormRequest
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
            'vins' => 'nullable|array|required_array_keys:value,filter_type',
            'vins.value' => 'required_with:vins|string',
            'vins.filter_type' => 'required_with:vins|string|in:equal,not_equal',
            "created_at" => 'nullable|string'
        ];
    }
}
