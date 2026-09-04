<?php

namespace App\Exports\Sheets;

use App\Models\Channel;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class ChannelMembersSheet implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    protected Channel $channel;

    public function __construct(Channel $channel)
    {
        $this->channel = $channel;
    }

    public function collection(): Collection
    {
        // members already eager loaded with their user relationship
        // no extra query happens here
        return $this->channel->members;
    }

    public function headings(): array
    {
        return ['Name', 'Email', 'Joined At'];
    }

    public function map($member): array
    {
        return [
            $member->user->name,
            $member->user->email,
            $member->created_at->format('Y-m-d'),
            // created_at on the Member pivot-like model = when they joined
        ];
    }

    public function title(): string
    {
        return 'Members';
    }
}