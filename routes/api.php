<?php

use App\Http\Controllers\Api\Auth\authController;
use App\Models\Setting;
use Illuminate\Support\Facades\Route;

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