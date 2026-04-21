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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::controller(AuthController::class)->group(function () {
    Route::post('/register', 'store');
    Route::post('/login', 'login');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::apiResource('users', UserController::class)->only(['index']);

    Route::apiResource('channels', ChannelController::class);

    Route::apiResource('time-entries', TimeEntryController::class);

    Route::apiResource('channels.invitations', InvitationController::class)->scoped();

    Route::post('shared-days', [SharedDayController::class, 'store']);
    Route::apiResource('channels.shared-days', SharedDayController::class)
        ->only(['index', 'destroy', 'show']);
    Route::apiResource('channels.shared-days.comments', CommentController::class)
        ->only(['index', 'update', 'destroy', 'store']);

    Route::get('reports', [ReportController::class, 'index']);

    Route::prefix('me')->group(function () {
        Route::get('shared-days',  [MeController::class, 'sharedDays']);
        Route::get('invitations',  [MeController::class, 'invitations']);
        Route::get('channels',     [MeController::class, 'channels']);
        Route::get('memberships',  [MeController::class, 'memberships']);
    });
});