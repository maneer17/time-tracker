<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\{Builder, Model};
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Carbon;

class TimeEntry extends Model
{
    use HasFactory, BelongsToOrganization;

    protected $fillable = [
        'user_id',
        'label',
        'date',
        'start_time',
        'end_time',
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d',
        'date'       => 'date:Y-m-d',
        'start_time' => 'datetime:h:i A',
        'end_time'   => 'datetime:h:i A',
    ];

    public static function baseRules(): array
    {
        return [
            'label'      => 'required|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time'   => 'required|date_format:H:i|after:start_time',
        ];
    }

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

        return $query->where('date', $date->format('Y-m-d'));
    }

    public function scopeToday(Builder $query): Builder
    {
        return $query->date(today());
    }

    public function scopeSearchByLabel(Builder $query, string $search): Builder
    {
        return $query->where('label', 'LIKE', '%' . $search . '%');
    }

    public function scopeSort(Builder $query, string $sort): Builder
    {
        return $query->when($sort ?? null,
            fn($q, $sort) => $q->orderBy('date', $sort)->orderBy('start_time', $sort),
            fn($q) => $q->orderBy('date', 'desc')->orderBy('start_time', 'desc')
        );
    }

    public function scopeHistory(Builder $query): Builder
    {
        return $query->select('date')
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
        ->when($filters['search'] ?? null,
            fn($q, $search) => $q->searchByLabel($search)
        );

        return $query;
    }

    public function scopeInRange($query, ?string $from, ?string $to): Builder
    {
        $from = $from
            ? Carbon::parse($from)->startOfDay()
            : Carbon::parse($query->min('date') ?? now()->subYear())->startOfDay();

        $to = $to
            ? Carbon::parse($to)->endOfDay()
            : now()->endOfDay();

        return $query->whereBetween('date', [$from, $to]);
    }

    public function sharedDayEntries()
    {
        return $this->hasMany(SharedDayEntry::class);
    }
}