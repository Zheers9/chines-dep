<?php

use App\Http\Controllers\Api\AccountingRegister\FeePaymentController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('accounting')->name('accounting.')->group(function () {
    Route::controller(FeePaymentController::class)->prefix('fee-payments')->name('fee-payments.')->group(function () {
        Route::get('', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::get('{id}', 'show')->name('show');
        Route::delete('{id}', 'destroy')->name('destroy');
        Route::post('', 'store')->name('store');
        Route::put('{id}', 'update')->name('update');
    });
});

