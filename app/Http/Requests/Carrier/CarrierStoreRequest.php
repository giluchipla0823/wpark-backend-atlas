<?php

namespace App\Http\Requests\Carrier;

use Illuminate\Foundation\Http\FormRequest;

class CarrierStoreRequest extends FormRequest
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
            'code' => 'required|min:3|max:20|unique:carriers,code',
            'is_train' => 'required|integer',
        ];
    }
}
