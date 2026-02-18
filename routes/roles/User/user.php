<?php

use App\Http\Controllers\Api\User\HomeController;
use Illuminate\Support\Facades\Route;

Route::controller(HomeController::class)->middleware('role:user')->prefix('user')->group(function () {
    Route::get('/', 'index');
});
