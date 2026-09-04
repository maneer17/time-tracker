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
        $owner->notify(new NewCommentNotification($comment));
        
    }
}