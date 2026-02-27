<?php

use App\Http\Controllers\Api\TopAdmin\ExamSubLevelController;
use App\Http\Controllers\Api\TopAdmin\ExamTypeController;
use App\Http\Controllers\Api\TopAdmin\FeeController;
use App\Http\Controllers\Api\TopAdmin\NotionController;
use App\Http\Controllers\Api\TopAdmin\SettingController;
use App\Http\Controllers\Api\TopAdmin\SubExamTypeController;
use App\Http\Controllers\Api\TopAdmin\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('top-admin')->name('top-admin.')->group(function () {
    Route::controller(UserController::class)->prefix('users')->name('users.')->group(function () {
        Route::get('', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::post('', 'store')->name('store');
        Route::get('{id}', 'show')->name('show');
        Route::put('{id}', 'update')->name('update');
        Route::delete('{id}', 'destroy')->name('destroy');
        Route::put('{id}/restore', 'restore')->name('restore');
    });
    Route::controller(SettingController::class)->prefix('settings')->name('settings.')->group(function () {
        Route::get('', 'index')->name('index');
        Route::post('', 'store')->name('store');
        Route::put('{id}', 'update')->name('update');
        Route::delete('{id}', 'destroy')->name('destroy');
    });
    Route::controller(ExamTypeController::class)->prefix('exam-types')->name('exam-types.')->group(function () {
        Route::get('', 'index')->name('index');
        Route::post('', 'store')->name('store');
        Route::put('{id}', 'update')->name('update');
        Route::delete('{id}', 'destroy')->name('destroy');
    });
    Route::controller(SubExamTypeController::class)->prefix('sub-exam-types')->name('sub-exam-types.')->group(function () {
        Route::get('', 'index')->name('index');
        Route::post('', 'store')->name('store');
        Route::put('{id}', 'update')->name('update');
        Route::delete('{id}', 'destroy')->name('destroy');
    });
    Route::controller(ExamSubLevelController::class)->prefix('exam-sub-levels')->name('exam-sub-levels.')->group(function () {
        Route::get('', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::post('', 'store')->name('store');
        Route::put('{id}', 'update')->name('update');
        Route::delete('{id}', 'destroy')->name('destroy');
    });
    Route::controller(FeeController::class)->prefix('fees')->name('fees.')->group(function () {
        Route::get('', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::get('{id}', 'show')->name('show');
        Route::post('', 'store')->name('store');
        Route::put('{id}', 'update')->name('update');
        Route::delete('{id}', 'destroy')->name('destroy');
    });
    Route::controller(NotionController::class)->prefix('notions')->name('notions.')->group(function () {
        Route::get('', 'index')->name('index');
        Route::post('', 'store')->name('store');
        Route::put('{id}', 'update')->name('update');
        Route::delete('{id}', 'destroy')->name('destroy');
    });
});