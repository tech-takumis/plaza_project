<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\Staff\StaffController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\Staff\MessageController;
use App\Http\Controllers\CertificateRequestController;
use App\Http\Controllers\Staff\StaffMessagesController;
use App\Http\Controllers\Staff\Auth\RegisteredStaffController;
use App\Http\Controllers\Staff\Auth\AuthenticatedStaffController;



Route::prefix('staff')->middleware(['guest:staff'])->group(function () {

    Route::post('/login', [AuthenticatedStaffController::class, 'store'])
        ->name('staff.login');
});

Route::prefix('staff')->middleware(['auth:staff'])->group(function () {

    Route::post('/register', [RegisteredStaffController::class, 'store'])
        ->name('staff.register');

    Route::post('/logout', [AuthenticatedStaffController::class, 'destroy'])
        ->name('staff.logout');

    Route::get('/certificates/requests', [CertificateRequestController::class, 'index'])
        ->name('staff.certificate.requests');
});


