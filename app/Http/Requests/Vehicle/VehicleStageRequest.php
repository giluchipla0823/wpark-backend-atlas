<?php

namespace App\Http\Requests\Vehicle;

use Illuminate\Foundation\Http\FormRequest;

class VehicleStageRequest extends FormRequest
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
            'tracking-date' => 'required|date_format:Y-m-d H:i:s.v',
            'lvin' => 'required|string|size:17',
            'pvin' => 'required|string|size:17',
            'station' => 'required|string|size:2',
            'eoc' => 'required|string|size:80',
            'manual' => 'nullable|boolean',
            'destination' => 'required|string|min:2|max:3'
        ];
    }
}
