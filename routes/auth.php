<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\UserMessageController;
use App\Http\Controllers\User\Auth\NewPasswordController;
use App\Http\Controllers\User\Auth\VerifyEmailController;
use App\Http\Controllers\User\Auth\RegisteredUserController;
use App\Http\Controllers\User\Auth\PasswordResetLinkController;
use App\Http\Controllers\User\Auth\AuthenticatedSessionController;
use App\Http\Controllers\User\Auth\EmailVerificationNotificationController;


Route::prefix('user')->middleware(['guest'])->group(function(){

    Route::post('/register', [RegisteredUserController::class, 'store'])
        ->middleware('guest')
        ->name('user.register');

    Route::post('/login', [AuthenticatedSessionController::class, 'store'])
        ->middleware('guest')
        ->name('user.login');

    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
        ->middleware('guest')
        ->name('user.password.email');

    Route::post('/reset-password', [NewPasswordController::class, 'store'])
        ->middleware('guest')
        ->name('user.password.store');

});

Route::prefix('user')->middleware(['auth'])->group(function(){

    Route::get('/verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware([ 'signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware([ 'throttle:6,1'])
        ->name('verification.send');

    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');

    Route::apiResource('/messages', UserMessageController::class);

});

