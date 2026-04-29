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

            $entryIds = TimeEntry::where('user_id', auth()->id())
                ->date($this->date)
                ->pluck('id');

            if ($entryIds->isEmpty()) {
                $validator->errors()->add('date', 'This day has no time entries.');
                return;
            }

            if ($this->entry_ids) {
                $invalid = collect($this->entry_ids)->diff($entryIds);

                if ($invalid->isNotEmpty()) {
                    $validator->errors()->add(
                        'entry_ids',
                        'Some entries do not belong to this day or to you.'
                    );
                }
            }
        });
    }
}