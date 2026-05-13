<?php

use App\Http\Controllers\Api\TopAdmin\AdminExamsController;
use App\Http\Controllers\Api\TopAdmin\AnnouncementController;
use App\Http\Controllers\Api\TopAdmin\ExamSubLevelController;
use App\Http\Controllers\Api\TopAdmin\ExamTypeController;
use App\Http\Controllers\Api\TopAdmin\FeeController;
use App\Http\Controllers\Api\TopAdmin\NotionController;
use App\Http\Controllers\Api\TopAdmin\RegisterController;
use App\Http\Controllers\Api\TopAdmin\SettingController;
use App\Http\Controllers\Api\TopAdmin\SubExamTypeController;
use App\Http\Controllers\Api\TopAdmin\UserController;
use App\Http\Controllers\Api\TopAdmin\AdminAboutController;
use App\Http\Controllers\Api\TopAdmin\AdminProgramController;
use App\Http\Controllers\Api\TopAdmin\AdminStaffMemberController;
use App\Http\Controllers\Api\TopAdmin\FeePaymentController;
use App\Http\Controllers\Api\TopAdmin\AdminHskScheduleController;
use App\Http\Controllers\Api\TopAdmin\SliderController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('top-admin')->name('top-admin.')->group(function () {
    Route::controller(AdminExamsController::class)->prefix('exams')->name('exams.')->group(function () {
        Route::get('roadmap/{sub_type_id}', 'getRoadmap')->name('roadmap');
        Route::get('steps/{id}', 'getStep');
        Route::post('steps', 'storeStep')->name('store-step');
        Route::put('steps/{id}', 'updateStep')->name('update-step');
        Route::post('steps/{id}', 'updateStep'); // Handle multipart updates
        Route::delete('steps/{id}', 'deleteStep')->name('delete-step');
        Route::post('steps/{step_id}/resources', 'storeResources')->name('store-resources');
        Route::post('steps/{step_id}/questions', 'storeQuestion')->name('store-question');
        Route::post('steps/{step_id}/questions/analyze-word', 'analyzeWord');
        Route::post('steps/{step_id}/questions/store-analyzed', 'storeAnalyzed');
        Route::post('questions/{id}', 'updateQuestion');
        Route::put('questions/{id}', 'updateQuestion');
        Route::delete('questions/{id}', 'deleteQuestion')->name('delete-question');
        Route::delete('files/{file_id}', 'deleteStepFile')->name('delete-file');
        
        // Sections
        Route::post('steps/{step_id}/sections', 'storeSection');
        Route::put('sections/{id}', 'updateSection');
        Route::delete('sections/{id}', 'deleteSection');
    });

    Route::controller(UserController::class)->prefix('users')->name('users.')->group(function () {
        Route::get('', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::post('', 'store')->name('store');
        Route::get('{id}', 'show')->name('show');
        Route::put('{id}', 'update')->name('update');
        // Delete functionality
        Route::delete('{id}', 'destroy')->name('destroy');
        Route::put('{id}/restore', 'restore')->name('restore');
    });

    Route::controller(SettingController::class)->prefix('settings')->name('settings.')->group(function () {
        Route::get('', 'index')->name('index');
        Route::post('', 'store')->name('store');
        Route::put('{id}', 'update')->name('update');
        Route::delete('{id}', 'destroy')->name('destroy');
        Route::put('{id}/toggle-active', 'toggleActive')->name('toggle-active');
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

    Route::controller(RegisterController::class)->prefix('registers')->name('registers.')->group(function () {
        Route::get('', 'index')->name('index');
        Route::put('{id}/toggle-accepted', 'toggleAccepted')->name('toggle-accepted');
        Route::delete('{id}', 'destroy')->name('destroy');
    });

    // Announcements
    Route::controller(AnnouncementController::class)->prefix('announcements')->name('announcements.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::get('/{id}', 'show')->name('show');
        Route::post('/{id}', 'update')->name('update'); // Use POST for multipart updates
        Route::delete('/{id}', 'destroy')->name('destroy');
    });

    // About Department
    Route::controller(AdminAboutController::class)->prefix('about')->group(function () {
        Route::post('', 'store');
        Route::post('stats/{id}', 'updateStat');
        Route::post('{id}', 'update');
        Route::delete('{id}', 'destroy');
    });

    // Programs & Courses
    Route::controller(AdminProgramController::class)->prefix('programs')->group(function () {
        Route::get('', 'index');
        Route::post('courses', 'storeCourse');
        Route::post('courses/{id}', 'updateCourse');
        Route::delete('courses/{id}', 'deleteCourse');
        Route::post('', 'store');
        Route::post('{id}', 'update');
        Route::delete('{id}', 'destroy');
    });

    // Staff & Lecturers
    Route::controller(AdminStaffMemberController::class)->prefix('staff')->group(function () {
        Route::post('', 'store');
        Route::delete('gallery/{id}', 'destroyGalleryImage');
        Route::post('{id}', 'update');
        Route::delete('{id}', 'destroy');
    });

    // Fee Payments
    Route::controller(FeePaymentController::class)->prefix('fee-payments')->group(function () {
        Route::get('', 'index');
        Route::post('', 'store');
        Route::put('{id}/toggle-paid', 'togglePaid');
        Route::delete('{id}', 'destroy');
    });

    // HSK Schedules CRUD
    Route::controller(AdminHskScheduleController::class)->prefix('hsk-schedules')->name('hsk-schedules.')->group(function () {
        Route::get('', 'index')->name('index');
        Route::post('', 'store')->name('store');
        Route::put('{id}', 'update')->name('update');
        Route::delete('{id}', 'destroy')->name('destroy');
    });

    // Sliders
    Route::controller(SliderController::class)->prefix('sliders')->group(function () {
        Route::get('', 'index');
        Route::post('', 'store');
        Route::post('{id}', 'update'); // Handle multipart updates
        Route::delete('{id}', 'destroy');
    });
});