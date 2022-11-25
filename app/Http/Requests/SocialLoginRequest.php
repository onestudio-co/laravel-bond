<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SocialLoginRequest extends FormRequest
{
    public function rules()
    {
        return [
            'provider' => [
                'required',
                'string',
                'in:google,apple',
            ],
            'token' => [
                'required',
                'string',
            ],
        ];
    }
}
