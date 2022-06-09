<?php

namespace App\Http\Requests\Device;

use Illuminate\Foundation\Http\FormRequest;

class DeviceStoreRequest extends FormRequest
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
            "name" => "required|string|max:255",
            "uuid" => "required|max:255|unique:devices,uuid",
            "device_type_id" => "required|integer|exists:devices_types,id",
            "version" => "nullable|max:255",
        ];
    }
}
