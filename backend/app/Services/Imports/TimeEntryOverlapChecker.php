<?php

namespace App\Services\Imports;

use App\Models\TimeEntry;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class TimeEntryOverlapChecker
{
    /**
     * Take the rows that PASSED per-row validation and split them again:
     * rows that don't collide stay valid; rows that overlap another file row
     * OR an existing saved entry move to errors.
     *
     * $validRows: collection of rows, each with line/date/start_time/end_time/label
     * $userId:    whose import this is (overlaps are per-user)
     */
    public function check(Collection $validRows, int $userId): array
    {
        // A row can only collide with something on the SAME date — bucket by date.
        $rowsByDate = $validRows->groupBy('date');

        // ONE query for all existing entries across all the file's dates, grouped by date.
        // This is what the (user_id, date) index is for.
        $existingByDate = TimeEntry::query()
            ->where('user_id', $userId)
            ->whereIn('date', $rowsByDate->keys())
            ->get(['date', 'start_time', 'end_time'])
            ->groupBy(fn ($entry) => $entry->date->format('Y-m-d'));

        // Overlap problems collected per line number (a row may hit several things).
        $overlapErrorsByLine = [];

        foreach ($rowsByDate as $date => $rowsOnThisDate) {
            $rows = $rowsOnThisDate->values();          // re-index 0,1,2 for the inner loops
            $existing = $existingByDate->get($date, collect());

            for ($i = 0; $i < $rows->count(); $i++) {
                $a = $rows[$i];

                // (a) against entries already saved in the DB for this date
                foreach ($existing as $db) {
                    if ($this->overlaps(
                        $a['start_time'], $a['end_time'],
                        $db->start_time, $db->end_time
                    )) {
                        $overlapErrorsByLine[$a['line']][] =
                            "Overlaps an existing saved entry on {$date}";
                    }
                }

                // (b) against LATER file rows on the same date.
                // j = i+1: check each pair once, never compare a row to itself.
                for ($j = $i + 1; $j < $rows->count(); $j++) {
                    $b = $rows[$j];

                    if ($this->overlaps(
                        $a['start_time'], $a['end_time'],
                        $b['start_time'], $b['end_time']
                    )) {
                        // Both rows flagged — neither is "the correct one".
                        $overlapErrorsByLine[$a['line']][] =
                            "Overlaps row {$b['line']} in this file";
                        $overlapErrorsByLine[$b['line']][] =
                            "Overlaps row {$a['line']} in this file";
                    }
                }
            }
        }

        // Split the incoming valid rows using the overlap findings.
        $valid  = collect();
        $errors = collect();

        foreach ($validRows as $row) {
            if (isset($overlapErrorsByLine[$row['line']])) {
                $errors->push([
                    'line'       => $row['line'],
                    'date'       => $row['date'],
                    'start_time' => $row['start_time'],
                    'end_time'   => $row['end_time'],
                    'label'      => $row['label'],
                    'errors'     => array_values(array_unique($overlapErrorsByLine[$row['line']])),
                ]);
            } else {
                $valid->push($row);
            }
        }

        return [
            'valid'  => $valid,
            'errors' => $errors,
        ];
    }

    /**
     * Do two time ranges overlap? Touching at a boundary is NOT an overlap.
     *
     * Each value may arrive as a "HH:MM" string (from the CSV) or as a Carbon
     * instance (from the DB, because the model casts these columns). Carbon::parse
     * handles both — and any format, 24h or AM/PM — so we never depend on the
     * string's exact shape. Then strict < on both sides (touching allowed).
     */
    private function overlaps($aStart, $aEnd, $bStart, $bEnd): bool
    {
        return $this->toMinutes($aStart) < $this->toMinutes($bEnd)
            && $this->toMinutes($bStart) < $this->toMinutes($aEnd);
    }

    /**
     * Normalize any time value (string OR Carbon, any format) to minutes-since-midnight.
     * This is the fix for the AM/PM cast bug: positional substr broke on "02:30 PM";
     * Carbon reads the actual hour/minute regardless of how the value is formatted.
     */
    private function toMinutes($time): int
    {
        $carbon = $time instanceof Carbon ? $time : Carbon::parse($time);

        return $carbon->hour * 60 + $carbon->minute;
    }
}