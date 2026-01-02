<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class TimeEntry extends Model
{
    use HasFactory;
    protected $fillable = [
    'user_id',
    'label',
    'start_time',
    'end_time',
    // ... any other fields
];
    public function user(){
        return $this->belongsTo(User::class);
    }
 public function scopeDate(Builder $query, string $date): Builder
{
    return $query->whereDate(
        'created_at',
        Carbon::parse($date)
    );
}

}
