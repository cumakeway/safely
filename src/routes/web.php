<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });


Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.action');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'role:worker,manager'])->group(function () {
    Route::post('/tasks/{task}/status', [TaskController::class, 'updateStatus']);
});

Route::middleware(['auth', 'role:manager'])->group(function () {
 Route::post('/create/task', [TaskController::class, 'store'])->name('task.create');
});

Route::get('/tasks', [TaskController::class, 'index'])
    ->name('tasks.index')
    ->middleware('auth');

Route::get('/tasks/{task}', [TaskController::class, 'show'])
    ->name('tasks.show')
    ->middleware('auth');