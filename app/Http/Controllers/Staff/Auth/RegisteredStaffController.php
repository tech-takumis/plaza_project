<?php

namespace App\Http\Controllers\Staff\Auth;

use App\Events\NewOfficialNotification;
use App\Http\Controllers\Controller;
use App\Models\Staff;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class RegisteredStaffController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): Response
    {
        $request->validate([
            // 'position' => ['required','in:kapitan,Kagawad,Chairperson,Secretary,Treasure'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'. Staff::class,'regex:/^[\w._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'position' => ['required',Rule::in(['Kapitan', 'Kagawad', 'Chairperson', 'Secretary', 'Treasure'])],
        ],[
            'position.in'=> 'The selected position must be one of the following: Kapitan, Kagawad, Chairperson, Secretary, or Treasure.',
        ]);

        $staff = Staff::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->string('password')),
            'position' => $request->position,
        ]);

        $new_staff = Staff::findOrFail($staff->id);

        Broadcast(new NewOfficialNotification($new_staff));

        return response()->noContent();
    }
}
