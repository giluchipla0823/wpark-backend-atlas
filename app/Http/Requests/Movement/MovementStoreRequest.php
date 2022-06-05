<?php

namespace App\Http\Requests\Movement;

use App\Models\Parking;
use App\Models\Slot;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MovementStoreRequest extends FormRequest
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
            'vehicle_id' => 'required|exists:vehicles,id',
            'origin_position_type' => 'required|max:255',
            'origin_position_id' => [
                'required',
                'integer',
                Rule::when($this->origin_position_type === Parking::class, ['exists:parkings,id']),
                Rule::when($this->origin_position_type === Slot::class, ['exists:slots,id'])
            ],
            'destination_position_type' => 'required|max:255',
            'destination_position_id' => [
                'required',
                'integer',
                Rule::when($this->destination_position_type === Parking::class, ['exists:parkings,id']),
                Rule::when($this->destination_position_type === Slot::class, ['exists:slots,id'])
            ],
            'comments' => 'nullable'
        ];
    }

}
