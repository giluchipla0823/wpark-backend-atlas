<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ForgotPasswordResetRequest extends FormRequest
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
            // 'email' => 'required|email|max:255',
            'username' => 'required|max:255|unique:users,username',
            'password' => 'required|max:100',
            'password_confirmation' => 'required|max:100|same:password',
            'token' => 'required'
        ];
    }
}
