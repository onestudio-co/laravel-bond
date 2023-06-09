<?php

namespace App\Http\Controllers;

use App\Events\DeletedUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogoutController extends Controller
{
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'logout success']);
    }

    public function destroy(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        event(new DeletedUser($user));

        $user->delete();
        return response()->json(['message' => 'account deleted successfully']);
    }
}
