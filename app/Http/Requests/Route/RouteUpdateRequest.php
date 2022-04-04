<?php

namespace App\Http\Requests\Route;

use Illuminate\Foundation\Http\FormRequest;

class RouteUpdateRequest extends FormRequest
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
        $id = $this->route('route')->id;

        return [
            'name' => 'required|max:255',
            'code' => "required|max:5|unique:routes,code,{$id},id",
            'origin_compound_id' => 'required|exists:compounds,id',
            'destination_compound_id' => 'required|exists:compounds,id',
            'comments' => 'nullable'
        ];
    }
}
