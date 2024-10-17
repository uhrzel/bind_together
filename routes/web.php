<?php

use App\Http\Controllers\ActivateUserController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\CalendarOfActivityController;
use App\Http\Controllers\CampusController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikedCommentController;
use App\Http\Controllers\NewsfeedController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\ReportedCommentController;
use App\Http\Controllers\SportController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::middleware('auth')->group(function () {
    Route::view('about', 'about')->name('about');

    Route::post('users/activate/{id}', ActivateUserController::class);
    Route::post('liked-comments', LikedCommentController::class)->name('liked-comments');
    Route::get('calendar-of-activities', CalendarOfActivityController::class)->name('calendar-of-activities');

    Route::resource('users', UserController::class);
    Route::resource('campus', CampusController::class);
    Route::resource('program', ProgramController::class);
    Route::resource('sport', SportController::class);
    Route::resource('organization', OrganizationController::class);
    Route::resource('newsfeed', NewsfeedController::class);
    Route::resource('comments', CommentController::class);
    Route::resource('reported-comment', ReportedCommentController::class);
    Route::resource('activity', ActivityController::class);

    Route::get('profile', [\App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
    Route::put('profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
});
