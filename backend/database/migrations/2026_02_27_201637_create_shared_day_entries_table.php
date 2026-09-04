<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use \App\Models\{SharedDay, TimeEntry};
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('shared_day_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(SharedDay::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(TimeEntry::class)->constrained()->cascadeOnDelete();
            $table->unique(['shared_day_id', 'time_entry_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shared_day_entries');
    }
};
