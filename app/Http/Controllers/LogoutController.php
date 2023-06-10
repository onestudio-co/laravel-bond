<?php

namespace App\Http\Controllers;

use App\Events\DeletedUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogoutController extends Controller
{
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'logout success']);
    }

    public function destroy(Request $request): JsonResponse
    {
        event(new DeletedUser($request->user()));

        return response()->json(['message' => __('account deleted successfully')]);
    }
}
