<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\{Builder, Model};
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};
use Illuminate\Database\Eloquent\Casts\Attribute;
class Channel extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'name', 'description'];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(Member::class);
    }


    public function invitations(): HasMany
    {
        return $this->hasMany(Invitation::class);
    }

    public function sharedDays(): HasMany
    {
        return $this->hasMany(SharedDay::class);
    }

    public function scopeOwned(Builder $query, User $user): Builder{
        return $query->where('user_id', $user->id);
    }

    public function scopeJoined(Builder $query, User $user ): Builder{
        return $query->whereHas('members', fn($q) => $q->where('user_id', $user->id));
    }
    public function isOwner(User $user): bool{
        return $this->user_id === $user->id;
    }
    public function isMember(User $user): bool{
        return $this->members()->where('user_id', $user->id)->exists();
    }
    public function isOwnerOrMember(User $user): bool{
        return $this->isOwner($user) || $this->isMember($user);
    }

















    
}
