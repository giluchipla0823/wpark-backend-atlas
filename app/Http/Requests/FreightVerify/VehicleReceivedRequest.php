<?php

namespace App\Http\Requests\FreightVerify;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use App\Services\External\FreightVerify\FreightVerifyService;

class VehicleReceivedRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return FreightVerifyService::getValidationRules([
            'transportationType' => [
                'required',
                'string',
                'max:2',
                Rule::in(['01', '02', '03', '04'])
            ]
        ]);
    }
}
