<?php

use App\Models\User;
use App\Mail\TestMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\UserController;

Route::get('/', function () {
    return ['Laravel' => app()->version()];
});




require __DIR__.'/auth.php';
require __DIR__.'/admin-auth.php';
