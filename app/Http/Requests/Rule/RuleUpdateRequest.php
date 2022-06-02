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

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {

        return [
            'name' => 'required|max:255',
            'priority' => 'required_if:is_group,0|integer|min:1',
            'is_group' => 'required|boolean',
            'predefined_zone_id' => 'nullable|exists:parkings,id',
            'carrier_id' => 'required_if:is_group,0|exists:carriers,id',
            'block_id' => 'nullable|exists:blocks,id,is_presorting,0',
            'conditions' => 'required_if:is_group,0|array',
            'rules' => 'required_if:is_group,1|array',
            'rules.*' => 'required_if:is_group,1|exists:rules,id,is_group,0',
            'active' => 'required_if:is_group,0|boolean',
        ];
    }

}
