<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function rules(): array
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
            ]
        ];
    }
}
