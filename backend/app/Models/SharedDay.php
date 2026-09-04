<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SharedDay extends Model
{
    use HasFactory;

    protected $fillable = ['channel_id', 'user_id', 'date'];

    protected $casts = [
        'date' => 'date:Y-m-d',
    ];
    protected $appends = ['entries_count', 'total_hours'];

    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }

    public function sharedBy()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function entries()
    {
        return $this->hasMany(SharedDayEntry::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    protected function totalTime(): Attribute
        {
            return Attribute::make(
                get: function () {
                    $totalMinutes = $this->entries->sum(function ($entry) {
                        $timeTaken = $entry->timeEntry->time_taken;
                        return ($timeTaken['hours'] * 60) + $timeTaken['minutes'];
                    });

                    return [
                        'hours'   => intdiv($totalMinutes, 60),
                        'minutes' => $totalMinutes % 60
                    ];
                }
            );
        }
    protected function entriesCount() : Attribute
    {
        return Attribute::make(
            get: fn()=> $this->entries()->count()
        );
    }    
}
