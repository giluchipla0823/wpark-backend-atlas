<?php

namespace App\Http\Requests\Condition;

use Illuminate\Foundation\Http\FormRequest;

class ConditionStoreRequest extends FormRequest
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
            'description' =>'nullable|max:255',
            'model_condition_id' => 'required|exists:model_conditions,id',
            'required' => 'required|boolean'
        ];
    }
}
