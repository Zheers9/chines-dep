<?php

use App\Http\Controllers\Api\User\HomeController;
use App\Http\Controllers\Api\User\RegisterController;
use Illuminate\Support\Facades\Route;

Route::controller(HomeController::class)->middleware('role:user')->prefix('user')->group(function () {
    Route::get('/', 'index');
});

Route::middleware('auth:sanctum')->prefix('user')->group(function () {
    Route::get('/my-registrations', [RegisterController::class, 'myRegistrations'])->name('user.my-registrations');
});
