<?php
namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\User;

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
