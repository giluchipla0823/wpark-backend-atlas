<?php

namespace App\Http\Requests\Design;

use Illuminate\Foundation\Http\FormRequest;

class DesignUpdateRequest extends FormRequest
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
        $id = $this->route('design')->id;

        return [
            'name' => 'required|max:255',
            'short_name' => 'nullable|max:255',
            'code' => "required|max:255|unique:designs,code,{$id},id",
            'brand_id' => 'required|exists:brands,id',
            'length' => 'required|integer|min:1',
            'width' => 'required|integer|min:1',
            'height' => 'required|integer|min:1',
            'weight' => 'required|integer|min:1',
            'description' => 'required|max:255',
            'manufacturing' => 'required|boolean',
            'svg' => 'nullable'
        ];
    }
}
