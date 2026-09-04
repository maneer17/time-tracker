<?php
// database/migrations/xxxx_xx_xx_swap_time_entry_indexes_to_date.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('time_entries', function (Blueprint $table) {
            // filters by `date`, not `created_at`, so these no longer serve a query
            $table->dropIndex('idx_created_at');
            $table->dropIndex('idx_user_created');
            $table->index(['user_id', 'date'], 'idx_user_date');
        });
    }

    public function down(): void
    {
        Schema::table('time_entries', function (Blueprint $table) {
            // reverse exactly — put back what we removed, drop what we added
            $table->dropIndex('idx_user_date');

            $table->index('created_at', 'idx_created_at');
            $table->index(['user_id', 'created_at'], 'idx_user_created');
        });
    }
};