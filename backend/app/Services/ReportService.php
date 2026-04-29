<?php
namespace App\Services;
use Illuminate\Support\Facades\{Cache, DB};
use App\Models\TimeEntry;
class ReportService
{

    public function generate(int $userId, ?string $from, ?string $to, ?bool $refresh = false): array
    {
        $cacheKey = "report_{$userId}_{$from}_{$to}";
        if($refresh){
            Cache::forget($cacheKey);
        }
        return Cache::remember($cacheKey, now()->addHours(24), function () use($userId, $from, $to) {
            return [
            'quick_stats'          => $this->quickStats($userId, $from, $to),
            'total_time_by_label'  => $this->totalTimeByLabel($userId, $from, $to),
            'most_used_labels'     => $this->mostUsedLabels($userId, $from, $to),
            'avg_time_per_label'   => $this->avgTimePerLabel($userId, $from, $to),
            'avg_labels_per_day'   => $this->avgLabelsPerDay($userId, $from, $to),
        ];

        });

    }

    private function quickStats(int $userId, ?string $from, ?string $to): array {
    $stats = TimeEntry::where('user_id', $userId)
        ->inRange($from, $to)
        ->selectRaw('
            COUNT(*) as total_entries,
            SUM(duration_minutes) as total_minutes,
            COUNT(DISTINCT DATE(created_at)) as total_days

        ')
        ->first();
        // here since I added a compound index  "idx_user_created" the sum and date functions 
        // won't do a full table scan rather it will just scan the rows that satisfy the condition .

    $totalMinutes     = $stats->total_minutes ?? 0;
    $totalDays        = $stats->total_days ?? 1;
    $avgMinutesPerDay = $totalDays > 0 ? intdiv($totalMinutes, $totalDays) : 0;

    return [
        'total_entries'       => $stats->total_entries ?? 0,
        'total_hours'         => intdiv($totalMinutes, 60),
        'total_minutes'       => $totalMinutes % 60,
        'total_days'          => $totalDays,
        'avg_hours_per_day'   => intdiv($avgMinutesPerDay, 60),
        'avg_minutes_per_day' => $avgMinutesPerDay % 60,
    ];

    }


    private function totalTimeByLabel(int $userId, ?string $from, ?string $to): array
    {
        return TimeEntry::where('user_id', $userId)
            ->inRange($from, $to)
            ->selectRaw('
                label,
                SUM(duration_minutes) as total_minutes,
                FLOOR(SUM(duration_minutes) / 60) as hours,
                MOD(SUM(duration_minutes), 60) as minutes
            ')
            ->groupBy('label')
            ->orderByDesc('total_minutes')
            ->get()
            ->toArray();
    }




    private function mostUsedLabels(int $userId, ?string $from, ?string $to): array
    {
        $totalMinutes = TimeEntry::where('user_id', $userId)
            ->inRange($from, $to)
            ->sum('duration_minutes');

        $rows = TimeEntry::where('user_id', $userId)
            ->inRange($from, $to)
            ->selectRaw('label, SUM(duration_minutes) as total_minutes')
            ->groupBy('label')
            ->orderByDesc('total_minutes')
            ->get();

        $top5    = $rows->take(5);
        $others  = $rows->skip(5);

        $result = $top5->map(fn($row) => [
            'label'      => $row->label,
            'hours'      => intdiv($row->total_minutes, 60),
            'minutes'    => $row->total_minutes % 60,
            'percentage' => $totalMinutes > 0
                ? round(($row->total_minutes / $totalMinutes) * 100, 1)
                : 0
        ])->toArray();

        if ($others->isNotEmpty()) {
            $othersMinutes = $others->sum('total_minutes');
            $result[] = [
                'label'      => 'Other',
                'hours'      => intdiv($othersMinutes, 60),
                'minutes'    => $othersMinutes % 60,
                'percentage' => $totalMinutes > 0
                    ? round(($othersMinutes / $totalMinutes) * 100, 1)
                    : 0
            ];
        }

        return $result;
    }





    private function avgTimePerLabel(int $userId, ?string $from, ?string $to): array
    {
        return TimeEntry::where('user_id', $userId)
            ->inRange($from, $to)
            ->selectRaw('
                label,
                AVG(duration_minutes) as avg_minutes,
                FLOOR(AVG(duration_minutes) / 60) as hours,
                MOD(AVG(duration_minutes), 60) as minutes
            ')
            ->groupBy('label')
            ->orderByDesc('avg_minutes')
            ->get()
            ->toArray();
    }
    private function avgLabelsPerDay(int $userId, ?string $from, ?string $to): float
    {
        $result = DB::select("
            SELECT ROUND(AVG(label_count), 1) as avg_labels
            FROM (
                SELECT COUNT(DISTINCT label) as label_count
                FROM time_entries
                WHERE user_id = ?
                AND created_at BETWEEN ? AND ?
                GROUP BY DATE(created_at)
            ) as daily_counts
        ", [$userId, $from, $to]);

        return $result[0]->avg_labels ?? 0;
    }


}
