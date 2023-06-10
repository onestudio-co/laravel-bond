<?php

namespace App\Http\Requests;

use App\Rules\ActiveUserEmail;
use Illuminate\Foundation\Http\FormRequest;

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
                new ActiveUserEmail(),
            ],
            'password' => [
                'required',
                'confirmed',
                'min:6',
            ],
        ];
    }
}
