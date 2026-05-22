<?php

namespace App\Listeners;

use App\Notifications\CommentDeletedByOwnerNotification;

class CommentDeletedByOwnerListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        $comment = $event->comment->load(['author','sharedDay.channel']);
        $comment->author->notify(new CommentDeletedByOwnerNotification($comment));
    }
}
