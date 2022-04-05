<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
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
        $id = $this->route('user')->id;

        return [
            'name' => 'required|max:75',
            'surname' => 'max:255',
            'email' => "required|email|max:255|unique:users,email,{$id},id",
            'username' => "required|max:255|unique:users,username,{$id},id",
            // 'admin_pin' => 'integer|digits_between:4,10' TODO: ¿Porqué este campo?
        ];
    }
}
