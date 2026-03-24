<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = ['shared_day_id', 'user_id', 'body'];
    protected $casts = [
        'created_at' => 'datetime: M d, Y h:i A',
    ];

    public function sharedDay(): BelongsTo
    {
        return $this->belongsTo(SharedDay::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
