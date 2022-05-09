<?php

namespace App\Http\Requests\DestinationCode;

use Illuminate\Foundation\Http\FormRequest;

class DestinationCodeUpdateRequest extends FormRequest
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
        $id = $this->route('destination_code')->id;

        return [
            'name' => 'required|max:255',
            'code' => "required|max:5|unique:destination_codes,code,{$id},id",
            'country_id' => 'required|exists:countries,id',
            'active' => 'required|boolean'
        ];
    }
}
