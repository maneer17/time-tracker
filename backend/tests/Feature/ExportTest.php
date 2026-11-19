<?php

namespace Tests\Feature;

use App\Jobs\ExportChannelDataJob;
use App\Models\User;
use App\Notifications\ChannelExportReadyNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Laravel\Sanctum\Sanctum;
use Maatwebsite\Excel\Facades\Excel;
use Tests\Concerns\BuildsTrackerData;
use Tests\TestCase;

class ExportTest extends TestCase
{
    use RefreshDatabase, BuildsTrackerData;

    private User $owner;
    private User $member;

    protected function setUp(): void
    {
        parent::setUp();
        $this->owner = User::factory()->create();
        $this->member = User::factory()->create();
    }

    public function test_the_channel_owner_can_start_a_background_channel_export(): void
    {
        Queue::fake();
        $channel = $this->channelOwnedBy($this->owner);
        Sanctum::actingAs($this->owner);

        $this->getJson("/api/exports/channel-data/{$channel->id}?file_extension=xlsx")
            ->assertStatus(202)
            ->assertJsonStructure(['message']);

        Queue::assertPushed(ExportChannelDataJob::class, fn ($job) => true);
    }

    public function test_only_the_owner_can_export_channel_data(): void
    {
        Queue::fake();
        $channel = $this->channelOwnedBy($this->owner);
        $this->addMember($channel, $this->member);

        foreach ([$this->member, User::factory()->create()] as $user) {
            Sanctum::actingAs($user);
            $this->getJson("/api/exports/channel-data/{$channel->id}?file_extension=xlsx")->assertForbidden();
        }

        Queue::assertNothingPushed();
    }

    public function test_channel_exports_only_allow_xlsx_or_pdf(): void
    {
        $channel = $this->channelOwnedBy($this->owner);
        Sanctum::actingAs($this->owner);

        $this->getJson("/api/exports/channel-data/{$channel->id}?file_extension=csv")
            ->assertStatus(422)->assertJsonValidationErrors('file_extension');
    }

    public function test_the_export_job_stores_the_file_and_notifies_the_owner_with_a_signed_link(): void
    {
        Carbon::setTestNow('2026-02-01 12:00:00');
        Excel::fake();
        Notification::fake();
        $channel = $this->channelOwnedBy($this->owner);
        $this->addMember($channel, $this->member);
        $this->shareDay($channel, $this->owner);

        (new ExportChannelDataJob($channel, $this->owner, 'xlsx'))->handle();

        $filename = "channel-{$channel->id}-".Carbon::now()->timestamp.'.xlsx';
        Excel::assertStored("exports/{$this->owner->id}/{$filename}", 'local');
        Notification::assertSentTo($this->owner, ChannelExportReadyNotification::class,
            fn ($notification) => str_contains($notification->toArray($this->owner)['download_url'], 'signature='));
    }

    public function test_export_downloads_require_a_valid_signature(): void
    {
        Storage::fake('local');
        Storage::put("exports/{$this->owner->id}/report.xlsx", 'file-contents');
        $params = ['user' => $this->owner->id, 'filename' => 'report.xlsx'];

        $this->get(route('exports.download', $params))->assertForbidden();
        $this->get(URL::temporarySignedRoute('exports.download', now()->addHour(), $params))->assertOk();
        $this->get(URL::temporarySignedRoute('exports.download', now()->subMinute(), $params))->assertForbidden();
    }

    public function test_members_can_export_a_shared_day_but_outsiders_cannot(): void
    {
        Excel::fake();
        $channel = $this->channelOwnedBy($this->owner);
        $this->addMember($channel, $this->member);
        $sharedDay = $this->shareDay($channel, $this->owner);

        Sanctum::actingAs(User::factory()->create());
        $this->getJson("/api/exports/shared-days/{$sharedDay->id}?file_extension=csv")->assertForbidden();

        Sanctum::actingAs($this->member);
        $this->get("/api/exports/shared-days/{$sharedDay->id}?file_extension=csv")->assertOk();
    }

    public function test_a_shared_day_can_be_exported_as_pdf(): void
    {
        $channel = $this->channelOwnedBy($this->owner);
        $sharedDay = $this->shareDay($channel, $this->owner);
        Sanctum::actingAs($this->owner);

        $response = $this->get("/api/exports/shared-days/{$sharedDay->id}?file_extension=pdf")->assertOk();

        $this->assertSame('application/pdf', $response->headers->get('Content-Type'));
    }

    public function test_time_entries_can_be_exported_for_a_date_range(): void
    {
        Excel::fake();
        Sanctum::actingAs($this->owner);

        $this->get('/api/exports/time-entries?start_date=2026-01-01&end_date=2026-01-31&file_extension=csv')
            ->assertOk();
        Excel::assertDownloaded('time_entries.csv');

        $this->getJson('/api/exports/time-entries?start_date=2026-02-01&end_date=2026-01-01&file_extension=csv')
            ->assertStatus(422)->assertJsonValidationErrors('end_date');
    }
}
