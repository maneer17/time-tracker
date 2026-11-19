<?php

namespace App\Listeners;

use App\Events\NewCommentEvent;
use App\Notifications\NewCommentNotification;

class NewCommentListener
{
    public function handle(NewCommentEvent $event): void
    {
        $comment = $event->comment;
        $owner = $comment->sharedDay->channel->owner;

        // The owner commenting on their own day doesn't need to be told about it
        if ($owner->id === $comment->user_id) {
            return;
        }

        $owner->notify(new NewCommentNotification($comment));
    }
}