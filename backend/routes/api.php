<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\{
    AuthController,
    ChannelController,
    InvitationController,
    TimeEntryController,
    SharedDayController,
    UserController,
    CommentController,
    MeController,
    ReportController
};

Route::controller(AuthController::class)->group(function () {
    Route::post('/register', 'store');
    Route::post('/login', 'login');
    Route::post('/auth/google', 'google');
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::apiResource('users', UserController::class)
        ->only(['index']);

    Route::apiResource('channels', ChannelController::class);

    Route::apiResource('time-entries', TimeEntryController::class);

    Route::apiResource('channels.invitations', InvitationController::class)
        ->scoped();

    Route::post('shared-days', [SharedDayController::class, 'store']);

    Route::apiResource('channels.shared-days', SharedDayController::class)
        ->only(['index', 'show', 'destroy']);

    Route::apiResource('shared-days.comments', CommentController::class)
        ->only(['index', 'store', 'update', 'destroy']);

    Route::get('reports', [ReportController::class, 'index']);

    Route::prefix('me')->group(function () {
        Route::get('shared-days', [MeController::class, 'sharedDays']);
        Route::get('invitations', [MeController::class, 'invitations']);
        Route::get('channels', [MeController::class, 'channels']);
        Route::get('memberships', [MeController::class, 'memberships']);
    });
});