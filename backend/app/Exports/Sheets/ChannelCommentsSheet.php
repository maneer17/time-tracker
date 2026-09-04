<?php
namespace App\Exports\Sheets;
use App\Models\Channel;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use App\Exports\Concerns\HasCommentMapping;

class ChannelCommentsSheet implements FromCollection, WithHeadings, WithTitle, WithMapping{
    use HasCommentMapping;
    
    protected Channel $channel;
    public function __construct(Channel $channel)
    {
        $this->channel = $channel;
    }

    public function collection(): Collection
    {
        return $this->channel->sharedDays
            ->flatMap(fn($day) => $day->comments);
    }


}

