<?php

namespace App\Http\Requests\DestinationCode;

use Illuminate\Foundation\Http\FormRequest;

class DestinationCodeStoreRequest extends FormRequest
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
            'code' => 'required|max:5|unique:destination_codes,code',
            'country_id' => 'required|exists:countries,id',
            'description' => 'nullable|max:255'
        ];
    }
}
