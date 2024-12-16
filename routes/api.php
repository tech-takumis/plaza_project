<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\Staff\StaffController;
use App\Http\Controllers\ActivityLogsController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\User\UserMessageController;
use App\Http\Controllers\CertificateRequestController;
use App\Http\Controllers\Staff\StaffMessagesController;

Route::middleware(['auth:sanctum', 'switch.database'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/users',[UserController::class, 'index'])
                ->name("allUser");

    // Certificate Route
    Route::get('/certificates', [CertificateController::class, 'index']);
    Route::post('/certificates', [CertificateController::class, 'store']);
    Route::get('/certificates/{id}', [CertificateController::class, 'show']);
    Route::put('/certificates/{id}', [CertificateController::class, 'update']);
    Route::delete('/certificates/{id}', [CertificateController::class, 'destroy']);

    // Certificate Request route
    Route::post('/certificates/requests', [CertificateRequestController::class, 'createRequest']);
    Route::get('/certificates/requests/my-requests', [CertificateRequestController::class, 'userCertificateRequst']);
    Route::put('/certificates/requests/{id}', [CertificateRequestController::class, 'update']);
    Route::delete('/certificates/requests/{id}', [CertificateRequestController::class, 'destroy']);

    // Certificate request approval/ reject route
    Route::post('/certificate-requests/{id}/approve', [CertificateRequestController::class, 'approveCertificate']);


    // Official Route
    Route::get('/officials', [StaffController::class, 'show_officials'])
        ->name('show.officials');
    Route::delete('/official/{id}', [StaffController::class, 'destroy_staff'])
        ->name('delete.staff');

    // Staff Message Route
    Route::post('/staff/messages', [StaffMessagesController::class, 'store'])
        ->name('messages');
    Route::get('/staff/messages', [StaffMessagesController::class, 'index'])
        ->name('staff.message');

    // User message route
    Route::get('/user/all/messages', [UserMessageController::class, 'all'])
        ->name('user.message');
    Route::post('/user/messages',[UserMessageController::class, 'store']);


    // Annoucement Route
    Route::get('/announcements', [AnnouncementController::class, 'index']);
    Route::get('/announcement', [AnnouncementController::class, 'latest']);
    Route::post('/announcements', [AnnouncementController::class, 'store']);
    Route::get('/announcements/{id}', [AnnouncementController::class, 'show']);
    Route::put('/announcements/{id}', [AnnouncementController::class, 'update']);
    Route::delete('/announcements/{id}', [AnnouncementController::class, 'destroy']);

    // Activity logs route
    Route::get('/activity/logs',[ActivityLogsController::class, 'index'])
        ->name('activity.logs');
    Route::get('/user/actions',[UserController::class, 'activityLogs']);


    });
