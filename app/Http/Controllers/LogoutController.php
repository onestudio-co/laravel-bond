<?php

namespace App\Http\Controllers;

class LogoutController extends Controller
{
    public function logout()
    {
        auth()->user()->tokens()->delete();

        return response()->json(['message' => 'logout success']);
    }
}
