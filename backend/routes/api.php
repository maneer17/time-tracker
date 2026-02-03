<?php
namespace App\Http\Middleware;
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


Route::middleware('guest')->controller(AuthController::class)->group(function () {
    Route::post('/register', 'store');  // Changed from /store to /register
    Route::post('/login', 'login');
});


Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    
    Route::get('time-entries/history', [TimeEntryController::class, "history"]);
    Route::get('time-entries/by-date/{date}', [TimeEntryController::class, "byDate"]);
    Route::apiResource('time-entries', TimeEntryController::class);
});




 

 
