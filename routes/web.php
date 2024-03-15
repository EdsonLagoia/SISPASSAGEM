<?php

use Illuminate\Support\Facades\Route;

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

use App\Http\Controllers\AccessController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\CompanionController;
use App\Http\Controllers\PartController;
use App\Http\Controllers\SpecialtyController;
use App\Http\Controllers\TravelController;


Route::get('/login', [AccessController::class, 'login'])->name('login');
Route::post('/authentication', [AccessController::class, 'authentication']);
Route::get('/change-password', [AccessController::class, 'change_password'])->middleware('auth');
Route::post('/new-password', [AccessController::class, 'new_password'])->middleware('auth');
Route::get('/', [AccessController::class, 'index'])->name('home')->middleware('auth');
Route::post('/logout', [AccessController::class, 'logout'])->middleware('auth');

Route::prefix('user')->group(function(){
    Route::get('/', [UserController::class, 'index']);
    Route::get('/create', [UserController::class, 'create']);
    Route::post('/store', [UserController::class, 'store']);
    Route::get('/{id}', [UserController::class, 'edit']);
    Route::put('/update/{id}', [UserController::class, 'update']);
    Route::put('/function/{id}', [UserController::class, 'function']);
});

Route::prefix('module')->middleware('auth')->group(function(){
    Route::get('/', [ModuleController::class, 'index']);
    Route::get('/create', [ModuleController::class, 'create']);
    Route::post('/store', [ModuleController::class, 'store']);
    Route::get('/{id}', [ModuleController::class, 'edit']);
    Route::put('/update/{id}', [ModuleController::class, 'update']);
    Route::put('/destroy/{id}', [ModuleController::class, 'destroy']);
});

Route::prefix('patient')->middleware('auth')->group(function(){
    Route::get('/', [PatientController::class, 'index']);
    Route::get('/create', [PatientController::class, 'create']);
    Route::post('/store', [PatientController::class, 'store']);
    Route::get('/{id}', [PatientController::class, 'edit']);
    Route::put('/update/{id}', [PatientController::class, 'update']);
    Route::put('/active/{id}', [PatientController::class, 'active']);
});

Route::prefix('companion')->middleware('auth')->group(function(){
    Route::get('/{patient}', [CompanionController::class, 'create']);
    Route::post('/store/{patient}', [CompanionController::class, 'store']);
    Route::get('/{patient}/{id}', [CompanionController::class, 'edit']);
    Route::put('/update/{patient}/{id}', [CompanionController::class, 'update']);
    Route::put('/active/{patient}/{id}', [CompanionController::class, 'active']);
});

Route::prefix('part')->middleware('auth')->group(function(){
    Route::get('/', [PartController::class, 'index']);
    Route::get('/create', [PartController::class, 'create']);
    Route::post('/store', [PartController::class, 'store']);
    Route::get('/{id}', [PartController::class, 'edit']);
    Route::put('/update/{id}', [PartController::class, 'update']);
    Route::put('/active/{id}', [PartController::class, 'active']);
});

Route::prefix('specialty')->middleware('auth')->group(function(){
    Route::get('/', [SpecialtyController::class, 'index']);
    Route::get('/create', [SpecialtyController::class, 'create']);
    Route::post('/store', [SpecialtyController::class, 'store']);
    Route::get('/{id}', [SpecialtyController::class, 'edit']);
    Route::put('/update/{id}', [SpecialtyController::class, 'update']);
    Route::put('/active/{id}', [SpecialtyController::class, 'active']);
});

Route::prefix('travel')->middleware('auth')->group(function(){
    Route::get('load-patient', [TravelController::class, 'loadPatient'])->name('loadPatient');
    Route::get('load-table', [TravelController::class, 'loadTable'])->name('loadTable');
    Route::get('load-companion', [TravelController::class, 'loadCompanion'])->name('loadCompanion');
    Route::get('load-number', [TravelController::class, 'loadNumber'])->name('loadNumber');
    Route::get('/', [TravelController::class, 'index']);
    Route::get('/create/{id}', [TravelController::class, 'create']);
    Route::post('/store/{id}', [TravelController::class, 'store']);
    Route::put('/cancel/{id}', [TravelController::class, 'cancel']);
    Route::get('/{id}', [TravelController::class, 'request']);
    Route::get('/report/{type?}/{id?}', [TravelController::class, 'report']);
});
