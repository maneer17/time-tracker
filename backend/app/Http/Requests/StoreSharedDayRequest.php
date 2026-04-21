<?php

namespace App\Http\Requests;
use App\Models\TimeEntry;
use Illuminate\Foundation\Http\FormRequest;

class StoreSharedDayRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'date'          => ['required', 'date'],
            'channel_ids'   => ['required', 'array', 'min:1'],
            'channel_ids.*' => ['required', 'exists:channels,id'],
            'entry_ids'     => ['required', 'array', 'min:1'],
            'entry_ids.*'   => ['required', 'exists:time_entries,id'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {

            $hasEntries = TimeEntry::where('user_id', auth()->id())
                ->date($this->date)
                ->exists();

            if (!$hasEntries) {
                $validator->errors()->add('date', 'This day has no time entries.');
            }


            if ($this->entry_ids) {
                $validCount = TimeEntry::where('user_id', auth()->id())
                    ->date($this->date)
                    ->whereIn('id', $this->entry_ids)
                    ->count();

                if ($validCount !== count($this->entry_ids)) {
                    $validator->errors()->add(
                        'entry_ids',
                        'Some entries do not belong to this day or to you.'
                    );
                }
            }
        });
    }
}