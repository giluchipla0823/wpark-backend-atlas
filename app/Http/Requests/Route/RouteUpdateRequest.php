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
        $id = $this->route('route')->id;

        return [
            'name' => 'required|max:255',
            'cdm_code' => "required|max:5|unique:routes,cdm_code,{$id},id",
            'origin_compound_id' => 'required|exists:compounds,id',
            'destination_compound_id' => 'nullable|exists:compounds,id',
            'comments' => 'nullable'
        ];
    }
}
