<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Language
{
    public function handle(Request $request, Closure $next)
    {
        if ($lang = $request->header('lang')) {
            app()->setLocale($lang);
        }
        if ($user = auth()->user()) {
            if ($lang && $user->locale !== $lang) {
                $user->update([
                    'locale' => $lang,
                ]);
            }
        }

        return $next($request);
    }
}
