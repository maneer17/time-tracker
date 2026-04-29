<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('time_entries', function (Blueprint $table) {
            $table->index('created_at', 'idx_created_at');
            $table->index(['user_id', 'created_at'],'idx_user_created' );
            $table->index(['label', 'user_id'], 'idx_user_label');
        });
    }
    public function down(): void
    {
        Schema::table('time_entries', function (Blueprint $table) {
            $table->dropIndex('idx_created_at');
            $table->dropIndex('idx_user_created');
            $table->dropIndex('idx_user_label');
        });
    }
};