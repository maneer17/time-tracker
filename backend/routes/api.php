<?php

use Illuminate\Http\Request;
use App\Models\Channel;
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
    ReportController,
    ExportController,
    ImportController,
    OrganizationController
};
use App\Http\Controllers\Api\Settings\NotificationPreferencesController;

// ── Public (no auth) ──────────────────────────────────────────
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
    Route::get('exports/download/{user}/{filename}', [ExportController::class, 'download'])
        ->name('exports.download');
});

Route::prefix('passwords')->group(function () {
    Route::post('forgot-password', [PasswordController::class, 'forgotPassword']);
    Route::post('reset-password', [PasswordController::class, 'resetPassword']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// ── Authenticated, NOT org-scoped (user-level data) ───────────
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::apiResource('users', UserController::class)->only(['index']);
    Route::apiResource('orgs', OrganizationController::class);
    Route::post('orgs/{org}/transfer-ownership', [OrganizationController::class, 'transferOwnership']);
    Route::delete('orgs/{org}/leave', [OrganizationController::class, 'leave']);
    // Email preferences are per-user, independent of org
    Route::prefix('settings')->group(function () {
        Route::get('/notification-preferences', [NotificationPreferencesController::class, 'index']);
        Route::put('/notification-preferences', [NotificationPreferencesController::class, 'update']);
    });
});

// ── Authenticated AND org-scoped (org-owned data) ─────────────
Route::middleware(['auth:sanctum', 'org'])->group(function () {

    Route::apiResource('channels', ChannelController::class);
    Route::apiResource('time-entries', TimeEntryController::class);

    Route::apiResource('channels.invitations', InvitationController::class)->scoped();

    Route::post('shared-days', [SharedDayController::class, 'store']);
    Route::apiResource('channels.shared-days', SharedDayController::class)
        ->only(['index', 'show', 'destroy']);

    Route::apiResource('shared-days.comments', CommentController::class)
        ->only(['index', 'store', 'update', 'destroy']);

    Route::get('reports', [ReportController::class, 'index']);

    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::get('/unread-count', [NotificationController::class, 'unreadCount']);
        Route::put('/mark-all-as-read', [NotificationController::class, 'markAllAsRead']);
        Route::put('/{notification}/read', [NotificationController::class, 'markAsRead']);
        Route::delete('/{notification}', [NotificationController::class, 'destroy']);
    });

    Route::prefix('exports')->group(function () {
        Route::get('time-entries', [ExportController::class, 'timeEntries']);
        Route::post('report', [ExportController::class, 'reportExport']);
        Route::get('shared-days/{sharedDay}', [ExportController::class, 'sharedDay']);
        Route::get('channel-data/{channel}', [ExportController::class, 'channelData']);
    });

    Route::prefix('me')->group(function () {
        Route::get('shared-days', [MeController::class, 'sharedDays']);
        Route::get('invitations', [MeController::class, 'invitations']); // channel invitations → org data
        Route::get('channels', [MeController::class, 'channels']);
        Route::get('memberships', [MeController::class, 'memberships']);
    });
});