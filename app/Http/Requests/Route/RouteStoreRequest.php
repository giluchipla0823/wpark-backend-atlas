<?php

namespace App\Http\Requests\Route;

use Illuminate\Foundation\Http\FormRequest;

class RouteStoreRequest extends FormRequest
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
            'cdm_code' => 'required|max:5|unique:routes,cdm_code',
            'origin_compound_id' => 'required|exists:compounds,id',
            'destination_compound_id' => 'nullable|exists:compounds,id',
            'comments' => 'nullable'
        ];
    }
}
