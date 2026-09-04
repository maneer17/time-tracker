<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use \App\Models\Invitation;
use \App\Models\User;
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('invitation_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Invitation::class);
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();
            $table->enum('action', ['sent', 'accepted', 'declined', 'cancelled'])->default('sent');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invitation_logs');
    }
};
