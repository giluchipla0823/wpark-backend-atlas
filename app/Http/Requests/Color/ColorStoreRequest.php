<?php

namespace App\Http\Requests\Color;

use Illuminate\Foundation\Http\FormRequest;

class ColorStoreRequest extends FormRequest
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
            'code' => 'required|max:255|unique:colors,code',
            'simple_name' => 'required|max:255',
            'hex' => 'required|max:255|unique:colors,hex'
        ];
    }
}
