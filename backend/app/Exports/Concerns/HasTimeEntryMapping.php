<?php
namespace App\Exports\Concerns;
trait HasTimeEntryMapping
{
    public function headings(): array
    {
        return ['Date', 'Start Time', 'End Time', 'Label', 'Duration'];
    }

    public function map($entry): array
    {
        return [
            $entry->created_at->format('Y-m-d'),
            $entry->start_time->format('h:i A'),
            $entry->end_time->format('h:i A'),
            $entry->label,
            $this->formatDuration($entry->time_taken),
        ];
    }

    private function formatDuration(array $timeTaken): string
    {
        $h = $timeTaken['hours'];
        $m = $timeTaken['minutes'];
        return $h > 0 ? "{$h}h {$m}m" : "{$m}m";
    }
}