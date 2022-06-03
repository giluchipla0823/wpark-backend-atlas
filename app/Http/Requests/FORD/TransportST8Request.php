<?php


namespace App\Http\Requests\FORD;


use Illuminate\Foundation\Http\FormRequest;

class TransportST8Request extends FormRequest
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
            'id' => 'required|string',
            'type' => 'required|string',
            'transportContent' => 'required|array:cdmCode,vehicles',
            'transportContent.*' => 'required', 
            'transportContent.*.cdmCode' => 'string|min:3', 
            'transportContent.*.vehicles' => 'array:vin,imported', 
            'transportContent.*.vehicles.*' => 'required', 
            'transportContent.*.vehicles.vin' => 'string|min:17|max:17', 
            'transportContent.*.vehicles.imported' => 'boolean'
        ];
    }
}
