<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\Api\TimeEntryController;
use \App\Http\Controllers\Api\AuthController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/store',[AuthController::class,'store']);
Route::post('/login',[AuthController::class,'login']);
Route::post('/logout',[AuthController::class,'logout'])->middleware('auth:sanctum');
Route::get('time-entries/history', [TimeEntryController::class, "history"])->name('time-entries.history');
Route::get('time-entries/by-date/{date}', [TimeEntryController::class, "byDate"])->name('time-entries.by-date');
Route::apiResource('time-entries', TimeEntryController::class);


 

 
