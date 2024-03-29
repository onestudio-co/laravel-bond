<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRegisterRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => [
                'nullable',
                'string',
            ],
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email'),
            ],
            'password' => [
                'required',
                'confirmed',
                'min:6',
            ],
        ];
    }

    public function messages()
    {
        return [
            'email.unique' => __('validation.active_user'),
        ];
    }
}
