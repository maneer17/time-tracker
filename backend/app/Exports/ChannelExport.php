<?php

namespace App\Exports;

use App\Exports\Sheets\ChannelCommentsSheet;
use App\Exports\Sheets\ChannelMembersSheet;
use App\Exports\Sheets\ChannelSharedDaysSheet;
use App\Models\Channel;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Override;

class ChannelExport implements  WithMultipleSheets
{
    protected Channel $channel;
    public function __construct(Channel $channel)
    {
        $this->channel= $channel;
    }

    #[Override]
    public function sheets(): array
    {
        return [
            new ChannelSharedDaysSheet($this->channel),
            new ChannelMembersSheet($this->channel),
            new ChannelCommentsSheet($this->channel)
        ];
    }


 
}
