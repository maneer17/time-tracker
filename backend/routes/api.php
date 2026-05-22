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
    NotificationController,
    PasswordController,
    ReportController
};
use App\Http\Controllers\Api\Settings\NotificationPreferencesController;
Route::controller(AuthController::class)->group(function () {
    Route::post('/register', 'store');
    Route::post('/login', 'login');
    Route::post('/auth/google', 'google');
});

Route::middleware('signed')->group(function () {
    Route::get('/invitations/{invitation}/accept', [InvitationController::class, 'accept'])
        ->name('invitations.accept');
    Route::get('/invitations/{invitation}/deny', [InvitationController::class, 'deny'])
        ->name('invitations.deny');       
    Route::get('/email/unsubscribe', [NotificationPreferencesController::class, 'unsubscribe'])
        ->name('email.unsubscribe'); 
});


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('passwords')->group(function (){
    Route::post('forgot-password', [PasswordController::class, 'forgotPassword']);
    Route::post('reset-password', [PasswordController::class, 'resetPassword']);

});



Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::prefix('notifications')->group(function () {
            Route::get('/', [NotificationController::class, 'index']);
            Route::get('/unread-count', [NotificationController::class, 'unreadCount']);
            Route::put('/mark-all-as-read', [NotificationController::class, 'markAllAsRead']);
            Route::put('/{notification}/read', [NotificationController::class, 'markAsRead']);
            Route::delete('/{notification}', [NotificationController::class, 'destroy']);
        });

    Route::apiResource('users', UserController::class)
        ->only(['index']);
    Route::prefix('settings')->middleware('auth:sanctum')->group(function () {
        Route::get('/notification-preferences', [NotificationPreferencesController::class, 'index']);
        Route::put('/notification-preferences', [NotificationPreferencesController::class, 'update']);
    });

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