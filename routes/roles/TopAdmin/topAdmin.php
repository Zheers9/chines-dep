<?php

use App\Http\Controllers\Api\TopAdmin\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('top-admin')->name('top-admin.')->group(function () {
    Route::controller(UserController::class)->prefix('users')->name('users.')->group(function () {
        Route::get('', 'index')->name('index');
        Route::post('', 'store')->name('store');
        Route::get('{id}', 'show')->name('show');
        Route::put('{id}', 'update')->name('update');
        Route::delete('{id}', 'destroy')->name('destroy');
    });
});