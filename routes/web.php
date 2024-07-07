<?php

use App\Http\Controllers\ControlPointController;
use App\Http\Controllers\DataRecordController;
use App\Http\Controllers\DroneController;
use App\Http\Controllers\MissionController;
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
Route::get('/about', [HomeController::class, 'about'])->name('home.about');
Route::get('/privacy', [HomeController::class, 'privacy'])->name('home.privacy');
Route::get('/rules', [HomeController::class, 'rules'])->name('home.rules');

Route::group(['middleware' => ['auth']], function () {
    Route::resource('user', UserController::class);
    Route::get('/user/{user}/delete', [UserController::class, 'destroy'])->name('user.delete');
    Route::resource('drone', DroneController::class);
    Route::get('/drone/{drone}/delete', [DroneController::class, 'destroy'])->name('drone.delete');
    Route::resource('mission', \App\Http\Controllers\MissionController::class);
    Route::get('/mission/{mission}/delete', [\App\Http\Controllers\MissionController::class, 'destroy'])->name('mission.delete');
    Route::resource('data_record', DataRecordController::class);

    Route::get('/missions/{mission}/data-records-async', [DataRecordController::class, 'async'])->name('dataRecord.async');
    Route::post('/data-records/store', [DataRecordController::class, 'store'])->name('dataRecord.store');
    Route::get('/data-records/{dataRecord}/delete', [DataRecordController::class, 'destroy'])->name('dataRecord.destroy');
    Route::delete('/data-records-ajax/{dataRecord}', [DataRecordController::class, 'destroyAjax'])->name('dataRecord.destroyAjax');
    Route::get('/missions/{mission}/drones-async', [DroneController::class, 'async'])->name('drones.async');
    Route::get('/missions/{mission}/control-points-async', [ControlPointController::class, 'async'])->name('controlPoints.async');
    Route::get('/missions/{mission}/statistics-async', [MissionController::class, 'statisticsAsync'])->name('statistics.async');
    Route::get('/missions/{mission}/statistics-recalculate', [MissionController::class, 'recalculateStatistics'])->name('statistics.recalculate');


});

