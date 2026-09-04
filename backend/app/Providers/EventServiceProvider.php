<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use \App\Events\{InvitationSent, AcceptedInvitationEvent, NewCommentEvent, NewSharedDayEvent, CommentDeletedByOwnerEvent};
use \App\Listeners\{NewInvitationListener, AcceptedInvitationListener, NewCommentListener, NewSharedDayListener, CommentDeletedByOwnerListener};
use App\Events\ChannelExportReadyEvent;
use App\Listeners\ChannelExportReadyListener;
class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        \SocialiteProviders\Manager\SocialiteWasCalled::class => [
            \SocialiteProviders\Google\GoogleExtendSocialite::class.'@handle',
        ],
        InvitationSent::class => [
            NewInvitationListener::class,
        ],
        AcceptedInvitationEvent::class => [
            AcceptedInvitationListener::class,
        ],
        NewCommentEvent::class => [
            NewCommentListener::class,
        ],
        NewSharedDayEvent::class => [
            NewSharedDayListener::class,
        ],
        CommentDeletedByOwnerEvent::class => [
            CommentDeletedByOwnerListener::class,
        ],
        ChannelExportReadyEvent::class => [
            ChannelExportReadyListener::class,
    ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
