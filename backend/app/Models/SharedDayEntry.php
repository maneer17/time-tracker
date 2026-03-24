<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SharedDayEntry extends Model
{
    use HasFactory;

    protected $fillable = ['shared_day_id', 'time_entry_id'];

    public function sharedDay(): BelongsTo
    {
        return $this->belongsTo(SharedDay::class);
    }

    public function timeEntry(): BelongsTo
    {
        return $this->belongsTo(TimeEntry::class);
    }
}
