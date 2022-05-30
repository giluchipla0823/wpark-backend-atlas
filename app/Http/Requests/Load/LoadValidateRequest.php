<?php


namespace App\Http\Requests\Load;


use Illuminate\Foundation\Http\FormRequest;

class LoadValidateRequest extends FormRequest
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
            "carrier_id" => "required|integer|exists:carriers,id",
            "vins" => "required|array",
            "vins.*" => "required|string|min:17|max:17"
        ];
    }
}
