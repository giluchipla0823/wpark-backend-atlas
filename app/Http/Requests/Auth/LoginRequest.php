<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
        $typesAccess = implode(",", [User::ACCESS_FROM_MOBILE_APP, User::ACCESS_FROM_WEB_APP]);

        return [
            "username" => "required|max:255",
            "password" => "required|max:100",
            "access_from" => "nullable|in:{$typesAccess}",
            "uuid" => "required_if:access_from," . User::ACCESS_FROM_MOBILE_APP,
        ];
    }
}
