<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\NotificationType;


class NotificationsTypes extends Model
{
    use HasFactory;
    protected $casts = [
    'notification_type' => NotificationType::class,
];
    protected $fillable = ['notification_type', 'user_id', 'mail'];
    public function user(): BelongsTo{
        return $this->belongsTo(User::class);
    }
}
