<?php

namespace App\Models;

use App\Models\Channel;
use App\Models\InvitationLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invitation extends Model
{
    use HasFactory;

    protected $fillable = ['channel_id', 'invited_id', 'invited_by_id', 'status'];

    public function channel(): BelongsTo
    {
        return $this->belongsTo(Channel::class);
    }

    public function invitedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_id');
    }

    public function invitedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(InvitationLog::class);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    public function scopeReceived(Builder $query, User $user): Builder
    {
        return $query->where('invited_id', $user->id);
    }

    public function scopeForChannel(Builder $query, Channel $channel): Builder
    {
        return $query->where('channel_id', $channel->id);
    }
}
