<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{Schema, DB};

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: add as nullable — existing rows can't have a value yet
        Schema::table('time_entries', function (Blueprint $table) {
            $table->date('date')->nullable()->after('user_id');
        });

        // Step 2: backfill — every existing row was created in real time,
        // so the day it was created on is the day it's for.
        DB::statement('UPDATE time_entries SET date = DATE(created_at) WHERE date IS NULL');

        // Step 3: now that every row has a value, enforce it going forward
        Schema::table('time_entries', function (Blueprint $table) {
            $table->date('date')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('time_entries', function (Blueprint $table) {
            $table->dropColumn('date');
        });
    }
};