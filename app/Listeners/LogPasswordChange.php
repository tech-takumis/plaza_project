<?php

namespace App\Listeners;

use App\Models\UserAction;
use Illuminate\Support\Facades\Request;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogPasswordChange
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(PasswordReset $event): void
    {
        UserAction::create([
            'user_id' => $event->user->id,
            'action_type' => 'password_change',
            'ip_address' => Request::ip(),
            'device_info' => Request::header('User-Agent')
        ]);
    }
}
