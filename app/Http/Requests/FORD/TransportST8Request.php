<?php
namespace App\Http\Requests\FORD;

use App\Exceptions\FORD\FordStandardErrorException;
use App\Helpers\ValidationHelper;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

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
            "id" => "required|string",
            "type" => "required|string",
            "transportContent" => "required|array|min:1",
            "transportContent.*" => "required",
            "transportContent.*.cdmCode" => "required|string|min:3",
            "transportContent.*.vehicles" => "required|array|min:1",
            "transportContent.*.vehicles.*.vin" => "required|string|min:17|max:17",
            "transportContent.*.vehicles.*.imported" => "required|boolean"
        ];
    }
}
