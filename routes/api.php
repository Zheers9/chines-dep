<?php

use App\Http\Controllers\Api\Auth\authController;
use App\Models\Setting;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TopAdmin\AnnouncementController;
use App\Http\Controllers\Api\TopAdmin\AdminAboutController;
use App\Http\Controllers\Api\TopAdmin\AdminProgramController;
use App\Http\Controllers\Api\TopAdmin\AdminStaffMemberController;
use App\Http\Controllers\Api\HskController;

Route::controller(authController::class)->group(function () {

    Route::post('signUp', 'signUp');
    Route::post('login', 'login')->middleware('guest');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', 'logout');
        Route::get('information_user', 'information_user');
        Route::post('profile', 'profile');
        Route::get('register', 'register');
        Route::post('registerForExam', 'registerForExam')->middleware('register_for_examing');

        require __DIR__ . '/roles/User/user.php';
        require __DIR__ . '/roles/TopAdmin/topAdmin.php';
        require __DIR__ . '/roles/Register/register.php';
        require __DIR__ . '/roles/Accounting/accounting.php';
    });
});

// Public Announcements
Route::get('announcements', [AnnouncementController::class, 'index']);
Route::get('about', [AdminAboutController::class, 'index']);
Route::get('programs', [AdminProgramController::class, 'index']);
Route::get('staff', [AdminStaffMemberController::class, 'index']);
Route::get('announcements/{id}', [AnnouncementController::class, 'show']);

// HSK Public Info
Route::controller(HskController::class)->prefix('hsk-info')->group(function () {
    Route::get('', 'getInfo');
    Route::get('roadmap/{sub_type_id}', 'getRoadmap');
    Route::get('step/{id}', 'getStep');
    Route::post('step/{id}/complete', 'completeStep')->middleware('auth:sanctum');
});