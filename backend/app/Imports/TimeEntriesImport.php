<?php

namespace App\Imports;
use App\Models\TimeEntry;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TimeEntriesImport implements ToCollection, WithHeadingRow
{
    /**
     * Parsed rows land here, each tagged with its real file line number.
     *
     * Why a property instead of a return value: Laravel Excel calls collection()
     * for us — it doesn't hand back whatever we return. So we capture the result
     * on the object and read it via $import->rows after Excel::import() runs.
     */
    public Collection $rows;


    public function __construct()
    {
        $this->rows = collect();
    }

    public function collection(Collection $entries): void
    {
        $entries->each(function ($entry, int $index) {
            // $index is 0-based over DATA rows (WithHeadingRow already ate line 1,
            // the header). The user's first data row is line 2 in their spreadsheet:
            //   index 0 -> line 2,  index 1 -> line 3, ...
            // so: line = index + 2.
            // NOTE: this +2 is the thing we must PROVE with a real file, not trust.
            $line = $index + 2;

            // ?? null: if a header is missing/misspelled, that column comes back
            // null instead of throwing. 
            $this->rows->push([
                'line'       => $line,
                'date'       => $entry['date']       ?? null,
                'start_time' => $entry['start_time'] ?? null,
                'end_time'   => $entry['end_time']   ?? null,
                'label'      => $entry['label']      ?? null,
            ]);
        });
    }
}