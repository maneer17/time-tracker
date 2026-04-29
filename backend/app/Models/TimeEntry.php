<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\{Builder, Model};
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Carbon;
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
        'start_time' => 'datetime:h:i A',
        'end_time'   => 'datetime:h:i A',
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


public function scopeDate(Builder $query, $date): Builder
{
    $date = $date instanceof Carbon ? $date : Carbon::parse($date);

    return $query
        ->where('created_at', '>=', $date->copy()->startOfDay())
        ->where('created_at', '<', $date->copy()->addDay()->startOfDay());
        // I added copy() because these carbon methdods actullay mutate the instance so we end up ranging the same value
}
        


public function scopeToday(Builder $query): Builder
{
    return $query->date(today());
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
            ->groupBy('date')
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
}


public function scopeInRange($query, ?string $from, ?string $to): Builder
{
    $from = $from
        ? Carbon::parse($from)->startOfDay()
        : Carbon::parse($query->min('created_at') ?? now()->subYear())->startOfDay();

    $to = $to
        ? Carbon::parse($to)->endOfDay()
        : now()->endOfDay();

    return $query->whereBetween('created_at', [$from, $to]);
}
// new
public function sharedDayEntries()
{
    return $this->hasMany(SharedDayEntry::class);
}
}
