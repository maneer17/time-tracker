<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('shared_days', function (Blueprint $table) {
            $table->unique(['channel_id', 'user_id', 'date'], 'unique_channel_user_date');
            $table->index('user_id', 'idx_user_id');
        });
    }

    public function down(): void
    {
        Schema::table('shared_days', function (Blueprint $table) {
            $table->dropUnique('unique_channel_user_date');
            $table->dropIndex('idx_user_id');
        });
}
};
