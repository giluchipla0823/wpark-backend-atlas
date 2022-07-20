<?php

namespace App\Http\Requests\Vehicle;

use App\Exceptions\FORD\FordStandardErrorException;
use App\Helpers\ValidationHelper;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class VehicleStageRequest extends FormRequest
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
     * @param Validator $validator
     * @return void
     * @throws FordStandardErrorException
     */
    protected function failedValidation(Validator $validator)
    {
        $errors = ValidationHelper::formatErrors($validator->errors()->toArray());

        $errors = array_map(function ($error) {
            return [
                'name' => $error['field'],
                'message' => $error['message'],
            ];
        }, $errors);

        $messages = [
            "Validation failed | Error count: " . count($errors)
        ];

        throw new FordStandardErrorException($messages, Response::HTTP_BAD_REQUEST, $errors);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'tracking-date' => 'required|date_format:Y-m-d H:i:s.v',
            'lvin' => 'required|string|size:17',
            'pvin' => 'required|string|size:17',
            'station' => 'required|string|size:2',
            'eoc' => 'required|string|size:80',
            'manual' => 'nullable|boolean',
            'destination' => 'required|string|min:2|max:3'
        ];
    }
}
