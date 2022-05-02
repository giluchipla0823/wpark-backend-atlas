<?php

namespace App\Http\Requests\Parking;

use Illuminate\Foundation\Http\FormRequest;

class ParkingDesignStoreRequest extends FormRequest
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
            'name' => 'required|max:255',
            'area_id' => 'required|exists:areas,id',
            'parking_type_id' => 'required|exists:parking_types,id',
            'qr' => 'required_if:parking_type_id,1,2|integer',
            'rows' => 'required_if:parking_type_id,1,2|array',
            'rows.count' => 'required_if:parking_type_id,1,2|integer|min:1',
            'rows.slots' => 'required_if:parking_type_id,1,2|array|min:1',
            'rows.slots.*' => 'required_if:parking_type_id,1,2|integer|min:1', // TODO: Ver como ponerle max: 1 si parking_type_id = 2
            'comments' => 'nullable'
        ];
    }
}
