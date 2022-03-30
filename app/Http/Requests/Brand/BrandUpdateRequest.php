<?php

namespace App\Http\Requests\Brand;

use Illuminate\Foundation\Http\FormRequest;

class BrandUpdateRequest extends FormRequest
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
        $id = $this->route('brand')->id;

        return [
            'name' => "required|max:255",
            'code' => "required|max:255|unique:brands,code,{$id},id",
            'compound_id' => "required|exists:compounds,id"
        ];
    }
}
