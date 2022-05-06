<?php

namespace App\Http\Requests\Hold;

use Illuminate\Foundation\Http\FormRequest;

class HoldStoreRequest extends FormRequest
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

    // TODO: Filtrar para que solo pueden añadirse condiciones del modelo Hold
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => 'required|max:255',
            'code' => 'required|max:255|unique:holds,code',
            'priority' => 'required|integer|min:1',
            'role_id' => 'required|exists:roles,id',
            'conditions' => 'required|array',
            'conditions.*' => 'exists:conditions,id'
        ];
    }
}
