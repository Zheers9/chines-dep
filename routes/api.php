<?php

use App\Http\Controllers\Api\Auth\authController;
use App\Models\Setting;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TopAdmin\AnnouncementController;
use App\Http\Controllers\Api\TopAdmin\AdminAboutController;
use App\Http\Controllers\Api\TopAdmin\AdminProgramController;
use App\Http\Controllers\Api\TopAdmin\AdminStaffMemberController;

Route::controller(authController::class)->group(function () {
    Route::post('signUp', 'signUp');
    Route::post('login', 'login');

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

Route::get('test', function () {
    return response()->json([
        'message' => Setting::query()->orderby('academic_year','desc')->first(),
    ]);
});

// Public Announcements
Route::get('announcements', [AnnouncementController::class, 'index']);
Route::get('about', [AdminAboutController::class, 'index']);
Route::get('programs', [AdminProgramController::class, 'index']);
Route::get('staff', [AdminStaffMemberController::class, 'index']);
Route::get('announcements/{id}', [AnnouncementController::class, 'show']);