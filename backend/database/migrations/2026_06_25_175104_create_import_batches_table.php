<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Enums\ImportStatus;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('import_batches', function (Blueprint $table) {
            $table->id(); 

            $table->foreignIdFor(User::class); // who owns this import — scopes overlaps & blocks cross-user access

            // lifecycle state: pending -> processing -> completed/failed
            $table->string('status')->default(ImportStatus::Pending->value);

            $table->string('file_name');   // the name the user uploaded — for display 
            $table->string('stored_path'); // where we parked the file so the worker can re-read it later 

            $table->unsignedInteger('total_rows')->nullable();    // rows in the file — known at preview
            $table->unsignedInteger('valid_rows')->nullable();    // rows that passed validation — known at preview
            $table->unsignedInteger('imported_rows')->nullable(); // rows actually inserted — null until the job runs
            $table->unsignedInteger('skipped_rows')->nullable();  // error rows dropped on partial import — null until the job runs

            $table->json('errors')->nullable();         // row-level problems, keyed by line number (e.g. "row 7 overlaps row 12")
            $table->text('failure_reason')->nullable(); // whole-job death (e.g. "file not found") — distinct from row errors; text so long messages aren't truncated

            $table->timestamps(); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('import_batches');
    }
};