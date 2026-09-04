<?php
namespace App\Exports\Sheets;
use App\Models\Channel;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Override;

class ChannelSharedDaysSheet implements FromCollection, WithHeadings, WithTitle, WithMapping{
    
    protected Channel $channel;
    public function __construct(Channel $channel)
    {
        $this->channel = $channel ;
    }

    public function collection(): Collection
    {
        return $this->channel->sharedDays;
    }

    #[Override]
    public function headings(): array
    {
        return [
            "Date",
            "Total Time",
             "Entries Count", 

        ];
    }

    #[Override]
    public function map($day): array
    {
        return [
            $day->date,
            $day->total_time,
            $day->entries_count, 
        ];
    }

    public function title():string{
        return "Shared Days";
    }


}

