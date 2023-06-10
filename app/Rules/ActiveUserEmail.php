<?php

namespace App\Rules;

use App\Models\User;
use Illuminate\Contracts\Validation\Rule;

class ActiveUserEmail implements Rule
{
    public function passes($attribute, $value)
    {
        return User::query()->where('email', $value)->whereNull('deleted_at')->count() === 0;
    }

    public function message()
    {
        return __('validation.active_user');
    }
}
