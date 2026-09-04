<?php

namespace App\Jobs;

use App\Models\Channel;
use App\Models\User;
use App\Exports\ChannelExport;
use App\Events\ChannelExportReadyEvent;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;

class ExportChannelDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Channel $channel;
    protected User $user;
    protected string $format;

    public function __construct(Channel $channel, User $user, string $format)
    {
        $this->channel = $channel;
        $this->user = $user;
        $this->format = $format;
    }

    public function handle(): void
    {
        $this->channel->load([
            'owner',
            'members.user',
            'sharedDays.comments.author',
            'sharedDays.entries',
        ]);

        $filename = "channel-{$this->channel->id}-" . now()->timestamp . '.' . $this->format;

        match ($this->format) {
            'xlsx' => Excel::store(
                new ChannelExport($this->channel),
                "exports/{$this->user->id}/{$filename}",
                'local'
            ),
            'pdf' => Pdf::loadView('exports.channel-data', [
                'channel' => $this->channel,
            ])->save(
                storage_path("app/exports/{$this->user->id}/{$filename}")
            ),
        };

        event(new ChannelExportReadyEvent($this->user, $filename, $this->channel));
    }
}