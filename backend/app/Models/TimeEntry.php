<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
class TimeEntry extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'label',
        'start_time',
        'end_time',
    ];
    
    protected $casts = [
        'created_at' => 'datetime:Y-m-d',
        'start_time' => 'datetime',
        'end_time' => 'datetime'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    protected function timeTaken(): Attribute
    {
        return Attribute::make(
            get: fn () => [
                'hours' => $this->start_time->diff($this->end_time)->h,
                'minutes' => $this->start_time->diff($this->end_time)->i
            ]
        );
    }


public function scopeDate(Builder $query, string $date): Builder
{
    return $query->whereDate(
        'created_at',
        Carbon::parse($date)
    );
}

public function scopeToday(Builder $query): Builder
{
    return $query->whereDate(
        'created_at', 
        today()  
    );
}
public function scopeSearchByLabel(Builder $query, string $search): Builder {
    return $query->where('label', 'LIKE', '%' . $search . '%');
}

public function scopeSort(Builder $query, string $sort): Builder
{
     $query->when($sort ?? null, 
        fn($q, $sort) => $q->orderBy('created_at', $sort), 
        fn($q) => $q->orderBy('created_at', 'desc')
    );
    return $query;

}

public function scopeHistory(Builder $query): Builder
{
    return $query->selectRaw('DATE(created_at) as date')
            ->distinct()
            ->orderBy('date', 'desc');

}

public function scopeSearch($query, array $filters): Builder
{
    $query->when($filters['date'] ?? null, 
        fn($q, $date) => $q->date($date), 
        fn($q) => $q->today()
    )
    ->when($filters['sort'] ?? null, 
        fn($q, $sort) => $q->sort($sort)
    )
    ->when($filters['search']?? null,
    fn($q,$search)=> $q->searchByLabel($search))
    ;
    
    return $query;  
}}