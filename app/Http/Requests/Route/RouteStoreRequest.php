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
        // TODO: Añadir carrier_id y dealer_id en fase 2
        return [
            'name' => 'required|max:255',
            'code' => 'required|max:5|unique:routes,code',
            'origin_compound_id' => 'required|exists:compounds,id',
            'destination_compound_id' => 'required|exists:compounds,id',
            'comments' => 'nullable'
        ];
    }
}
