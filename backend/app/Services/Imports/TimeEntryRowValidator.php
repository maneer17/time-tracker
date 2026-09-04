<?php

namespace App\Services\Imports;

use App\Models\TimeEntry;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;

class TimeEntryRowValidator
{
    /**
     * The rules for a single CSV row: the shared base rules + the date rule
     * (the CSV supplies the date, so unlike the single-entry form, we validate it here).
     */
    private function rules(): array
    {
        return array_merge(TimeEntry::baseRules(), [
            'date' => 'required|date_format:Y-m-d',
        ]);
    }

    /**
     * Validate every row on its own (format/required checks — NOT overlap).
     * Crucially: it does NOT stop at the first bad row. Every row is checked,
     * so the user sees ALL their problems at once, not one-at-a-time.
     */
    public function validate(Collection $rows): array
    {
        $valid  = collect();
        $errors = collect();

        foreach ($rows as $row) {
            $validator = Validator::make($row, $this->rules());

            if ($validator->fails()) {
                $errors->push([
                    'line'   => $row['line'],
                    'date'       => $row['date'],
                    'start_time' => $row['start_time'],
                    'end_time'   => $row['end_time'],
                    'label'      => $row['label'],
                    'errors' => $validator->errors()->all(),
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
}