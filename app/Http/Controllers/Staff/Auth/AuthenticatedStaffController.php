<?php

namespace App\Http\Controllers\Staff\Auth;

use App\Models\Staff;
use App\Mail\TestMail;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Events\IsNotActiveEvent;
use Illuminate\Http\JsonResponse;
use App\Events\IsActiveStaffEvent;
use App\Services\DatabaseSwitcher;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\Auth\StaffRequest;
use Illuminate\Support\Facades\Broadcast;

class AuthenticatedStaffController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function store(StaffRequest $request): JsonResponse
    {
            $request->authenticate();

            $request->session()->regenerate();

            $auth = Auth::guard('staff')->user();


            $staff = Staff::find($auth->id);


            $staff->is_active = true;
            $staff->save();

            broadcast(new IsActiveStaffEvent($staff))->toOthers();

            $token = $staff->createToken('API Token')->plainTextToken;

            return response()->json([
                'token' => $token,
                'message' => 'You login successfully'
            ]);

    }


    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): Response
    {
        $staff = Auth::guard('staff')->user();

        $authStaff = Staff::where('email',$staff->email)->first();


        $authStaff->is_active = false;

        $authStaff->save();

        Broadcast(new IsNotActiveEvent($authStaff));

        Auth::guard('staff')->logout();


        $request->session()->invalidate();

        $request->session()->regenerateToken();


        return response()->noContent();
    }
}
