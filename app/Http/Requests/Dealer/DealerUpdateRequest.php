<?php

namespace App\Http\Requests\Dealer;

use Illuminate\Foundation\Http\FormRequest;

class DealerUpdateRequest extends FormRequest
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
        $id = $this->route('dealer')->id;

        return [
            'name' => 'required|max:255',
            'code' => "required|min:3|max:10|unique:dealers,code,{$id},id",
            'zip_code' => 'required|max:255',
            'city' => 'required|max:255',
            'street' => 'required|max:255',
            'country' => 'required|max:255'
        ];
    }
}
