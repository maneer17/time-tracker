<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Organization extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'settings',
    ];

    protected $casts = [
        'settings' => 'array', // JSON column <-> PHP array
    ];

    // The membership rows (carry the role) — use when you need roles
    public function members(): HasMany
    {
        return $this->hasMany(OrganizationUser::class);
    }

    // The actual users in this org — use when you just need User models
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'organization_user')
                    ->withPivot('role')
                    ->withTimestamps();
    }
}