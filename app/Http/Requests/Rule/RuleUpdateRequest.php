<?php

namespace App\Http\Requests\Rule;

use Illuminate\Foundation\Http\FormRequest;

class RuleUpdateRequest extends FormRequest
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

    // TODO: Filtrar para que solo pueden añadirse condiciones del modelo Rule
    // TODO: Falta añadir relaciones, pendiente de video de Vicente para sacarlas
    // TODO: Validar que los conditionable_id existen en sus respectivas tablas
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => 'required|max:255',
            'priority' => 'required|integer|min:1',
            'active' => 'required|boolean',
            'blocks' => 'required|array',
            'blocks.*' => 'exists:blocks,id',
            'conditions' => 'required|array',
        ];
    }
}
