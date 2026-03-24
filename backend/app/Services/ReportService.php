<?php
namespace App\Services;

use App\Models\TimeEntry;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
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
        ->selectRaw('count(*) as total_entries,
        SUM(duration_minutes) as total_minutes,
        COUNT(DISTINCT DATE(created_at)) as total_days
        ')->first();
        $totalMinutes = $stats->total_minutes ?? 0;
        $totalDays    = $stats->total_days ?? 1;
        $avgMinutesPerDay = $totalDays > 0 ? intdiv($totalMinutes, $totalDays) : 0;
        return [
            "total_entries"    => $stats->total_entries ?? 0,
            "total_hours" => intDiv($totalMinutes,60) ,
            "total_minutes" => $totalMinutes % 60,
            "total_days" => $totalDays,
            "avg_hours_per_day" => intdiv($avgMinutesPerDay, 60),
            "avg_minutes_per_day" => $avgMinutesPerDay % 60,
        ];

    }


    private function totalTimeByLabel(int $userId, ?string $from, ?string $to): array
    {
        $rows = TimeEntry::where('user_id', $userId)
            ->inRange($from, $to)
            ->selectRaw('label, SUM(duration_minutes) as total_minutes')
            ->groupBy('label')
            ->orderByDesc('total_minutes')
            ->get();

        return $rows->map(fn($row) => [
            'label'   => $row->label,
            'hours'   => intdiv($row->total_minutes, 60),
            'minutes' => $row->total_minutes % 60,
        ])->toArray();
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
        $rows = TimeEntry::where('user_id', $userId)
            ->inRange($from, $to)
            ->selectRaw('label, AVG(duration_minutes) as avg_minutes')
            ->groupBy('label')
            ->orderByDesc('avg_minutes')
            ->get();

        return $rows->map(fn($row) => [
            'label'   => $row->label,
            'hours'   => intdiv((int) $row->avg_minutes, 60),
            'minutes' => (int) $row->avg_minutes % 60,
        ])->toArray();
    }

    private function avgLabelsPerDay(int $userId, ?string $from, ?string $to): float
    {
        $rows = TimeEntry::where('user_id', $userId)
            ->inRange($from, $to)
            ->selectRaw('DATE(created_at) as date, COUNT(DISTINCT label) as label_count')
            ->groupByRaw('DATE(created_at)')
            ->get();

        if ($rows->isEmpty()) return 0;

        return round($rows->avg('label_count'), 1);
    }


}
