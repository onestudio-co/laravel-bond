<?php

namespace App\Rules;

use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ActiveUserEmail implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (User::query()->where('email', $value)->whereNull('deleted_at')->count() === 0) {
            $fail('validation.active_user')->translate();
        }
    }
}
