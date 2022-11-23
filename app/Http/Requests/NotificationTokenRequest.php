<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NotificationTokenRequest extends FormRequest
{
    public function rules()
    {
        return [
            'device_id' => [
                'string',
                'required',
            ],
            'device_type' => [
                'string',
                'in:ios,android,web',
            ],
            'token' => [
                'string',
                'required',
            ],
        ];
    }
}