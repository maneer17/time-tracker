<?php

namespace App\Models;

use App\Enums\ImportStatus;
use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportBatch extends Model
{
    use BelongsToOrganization;
    protected $fillable = [
        'user_id',
        'status',
        'file_name',
        'stored_path',
        'total_rows',
        'valid_rows',
        'imported_rows',
        'skipped_rows',
        'errors',
        'failure_reason',
    ];
    protected $casts = [
        'status' => ImportStatus::class,
        'errors' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


    public function markProcessing(): void
    {
        $this->update(['status' => ImportStatus::Processing]);
    }

    public function markCompleted(int $imported, int $skipped, array $errors = []): void
    //a completed import might still have skipped rows with reasons. Completed doesn't mean "zero errors
    {
        $this->update([
            'status'        => ImportStatus::Completed,
            'imported_rows' => $imported,
            'skipped_rows'  => $skipped,
            'errors'        => $errors,
        ]);
    }

    public function markFailed(string $reason): void
    {
        $this->update([
            'status'         => ImportStatus::Failed,
            'failure_reason' => $reason,
        ]);
    }
}