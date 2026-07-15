<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\ChatConversationController;
use App\Http\Controllers\ChatMessageController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PublicActivityAnswerController;
use App\Http\Controllers\PublicActivityController;
use App\Http\Controllers\PublicActivityMediaController;
use App\Http\Controllers\PublicActivityResultController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentSelectionController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'welcome')->name('home');
Route::get('activities/{activity}', PublicActivityController::class)->name('activities.show');
Route::get('activities/{activity}/media/{mediaId}', PublicActivityMediaController::class)
    ->where('mediaId', '[a-z][a-z0-9_-]*')
    ->name('activities.media.show');
Route::post('activities/{activity}/answers', PublicActivityAnswerController::class)
    ->middleware('throttle:60,1')
    ->name('activities.answers.store');
Route::post('activities/{activity}/result', PublicActivityResultController::class)
    ->middleware('throttle:60,1')
    ->name('activities.result.store');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');
    Route::get('students', [StudentController::class, 'index'])->name('students.index');
    Route::post('students', [StudentController::class, 'store'])->name('students.store');
    Route::put('students/{student}', [StudentController::class, 'update'])->name('students.update');
    Route::post('students/{student}/select', StudentSelectionController::class)->name('students.select');
    Route::get('chat', ChatController::class)->name('chat.index');
    Route::post('chat/messages', ChatMessageController::class)
        ->middleware('throttle:activity-agent')
        ->name('chat.messages.store');
    Route::delete('chat/conversation', ChatConversationController::class)
        ->name('chat.conversation.destroy');
});

require __DIR__.'/settings.php';
