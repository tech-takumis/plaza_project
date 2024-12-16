<?php

namespace App\Http\Controllers\User\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Services\DatabaseSwitcher;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Auth\LoginRequest;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): JsonResponse
    {
            $request->authenticate();

            $request->session()->regenerate();

            $authUser = Auth::user();

            if (!$authUser) {
                return response()->json(['errors' => 'User not authenticated'], 401);
            }

            $user = User::find($authUser->id);

            $user->is_active = true;
            $user->save();

            $token = $user->createToken('API Token')->plainTextToken;

            return response()->json(['token' => $token]);

    }


    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): Response
    {
        $auth = Auth::user();
        Auth::guard('web')->logout();

        $user = User::find($auth->id);

        $user->is_active = false;
        $user->save();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return response()->noContent();
    }
}
