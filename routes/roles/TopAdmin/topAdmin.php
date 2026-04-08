<?php

use App\Http\Controllers\Api\TopAdmin\ExamSubLevelController;
use App\Http\Controllers\Api\TopAdmin\ExamQuestionController;
use App\Http\Controllers\Api\TopAdmin\ExamRoadmapController;
use App\Http\Controllers\Api\TopAdmin\ExamTypeController;
use App\Http\Controllers\Api\TopAdmin\FeeController;
use App\Http\Controllers\Api\TopAdmin\NotionController;
use App\Http\Controllers\Api\TopAdmin\PostController;
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
    Route::controller(PostController::class)->prefix('posts')->name('posts.')->group(function () {
        Route::get('', 'index')->name('index');
        Route::get('{id}', 'show')->name('show');
        Route::post('', 'store')->name('store');
        Route::post('{id}', 'update')->name('update');
        Route::delete('{id}', 'destroy')->name('destroy');
    });
    Route::controller(ExamRoadmapController::class)->prefix('exams')->name('exams.')->group(function () {
        Route::get('roadmap/{subTypeId}', 'indexBySubType')->name('roadmap');
        Route::post('steps', 'store')->name('steps.store');
        Route::delete('steps/{id}', 'destroy')->name('steps.destroy');
    });
    Route::controller(ExamQuestionController::class)->prefix('exams')->name('exam-questions.')->group(function () {
        Route::get('steps/{stepId}/questions', 'indexByStep')->name('index-by-step');
        Route::post('steps/{stepId}/questions', 'store')->name('store');
        Route::post('steps/{stepId}/questions/analyze-word', 'analyzeWord')->name('analyze-word');
        Route::post('steps/{stepId}/questions/store-analyzed', 'storeAnalyzed')->name('store-analyzed');
        Route::delete('questions/{id}', 'destroy')->name('destroy');
    });
});