<?php

namespace App\Http\Controllers\User\Auth;

use App\Mail\TestMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rules;
use Illuminate\Http\JsonResponse;
use App\Services\DatabaseSwitcher;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Residents;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Mail;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'address' => ['required','string','max:255'],
            'contact_number' => ['nullable','string','max:255'],
            'civil_status' => ['nullable'],
            'occupation' => ['nullable','string','max:255'],
            'Residency_Start_Date' => ['nullable','date'],
            'Remarks' => ['nullable','string','max:255']
        ]);

        $resident = Residents::create([
            'address' => $request->address,
            'contact_number' => $request->contact_number,
            'civil_status'=> $request->civil_status,
            'occupation' => $request->occupation,
            'Residency_Start_Date' => $request->residency_start_date,
            'Remarks' => $request->remarks
        ]);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->string('password')),
            'resident_id' => $resident->id,
            'status' => true
        ]);


        event(new Registered($user));

        Mail::to($user->email)->send(new TestMail($user));

        Auth::login($user);


        $token = $user->createToken('API Token')->plainTextToken;


        return response()->json(['token' => $token]);
    }
}
