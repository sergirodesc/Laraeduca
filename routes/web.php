<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\TaskManager;
use App\Models\Task;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect('dashboard');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    Route::prefix('admin')->group(function () {
        Route::get('/user-management', function () {
            return view('user-management');
        })->name('user-management');
        Route::get('/courses', function () {
            return view('teams-list');
        })->name('courses');
        Route::get('/attendance', function () {
            return view('schedule-admin');
        })->name('admin-attendance');
    });
    Route::get('/tasks', function () {
        return view('react-tasks.index');
    })->name('tasks');
    Route::get('/task/{task}', function(Task $task) {
        return view('task', compact('task'));
    })->middleware(['auth'])->name('task');
    Route::get('/games', function () {
        return view('react-tasks.index-react');
    })->name('games');
    Route::prefix('games')->group(function () {
        Route::get('/music-task', function () {
            return view('react-tasks.music-task');
        })->name('music-task');
        Route::get('/simon-task', function () {
            return view('react-tasks.simon-task');
        })->name('simon-task');
        Route::get('/hangman-task', function () {
            return view('react-tasks.hangman-task');
        })->name('hangman-task');
    });

    Route::get('/attendance', function () {
        return view('schedule-student');
    })->name('student-attendance');
});
