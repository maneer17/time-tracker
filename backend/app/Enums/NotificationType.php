<?php

namespace App\Enums;

enum NotificationType: string 
{
    case ChannelInvitations  = 'channel_invitations';
    case AcceptedInvitations = 'accepted_invitations';
    case NewSharedDays       = 'new_shared_days';
    case NewComments         = 'new_comments';
    case CommentDeletedByOwner = 'comment_deleted_by_owner';
    case ChannelExportReady = 'channel_export_ready';
}