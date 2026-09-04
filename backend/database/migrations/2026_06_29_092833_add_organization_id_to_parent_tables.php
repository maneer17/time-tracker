<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Top-level org tables. Children inherit org through their parent,
     * so they don't carry organization_id (verified against query patterns).
     */
    private array $tables = [
        'time_entries'   => 'user_id',
        'channels'       => 'user_id',
        'import_batches' => 'user_id',
    ];

    public function up(): void
    {
        foreach ($this->tables as $table => $pairColumn) {
            Schema::table($table, function (Blueprint $t) use ($pairColumn) {
                // nullable for now — existing rows backfilled in the next migration
                $t->foreignId('organization_id')->nullable()->constrained();
                // org leads the composite: it's the equality filter on every query
                $t->index(['organization_id', $pairColumn]);
            });
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->dropConstrainedForeignId('organization_id');
            });
        }
    }
};