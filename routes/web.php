<?php

use App\Http\Controllers\DataRecordController;
use App\Http\Controllers\DroneController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\HomeController;

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
    return view('home');
});

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::group(['middleware' => ['auth']], function () {
    Route::resource('user', UserController::class);
    Route::get('/user/{user}/delete', [UserController::class, 'destroy'])->name('user.delete');
    Route::resource('drone', DroneController::class);
    Route::get('/drone/{drone}/delete', [DroneController::class, 'destroy'])->name('drone.delete');
    Route::resource('mission', \App\Http\Controllers\MissionController::class);
    Route::get('/mission/{mission}/delete', [\App\Http\Controllers\MissionController::class, 'destroy'])->name('mission.delete');
    Route::get('/recipes/{recipe}', [\App\Http\Controllers\MissionController::class, 'show'])->name('recipe.show');
    Route::get('/missions/{mission}/data-records', [DataRecordController::class, 'index'])->name('dataRecord.index');
    Route::post('/data-records', [DataRecordController::class, 'store'])->name('dataRecord.store');
});

