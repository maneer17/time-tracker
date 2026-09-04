<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Enums\OrganizationRole;
use App\Models\Organization;
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void{
        Schema::create('organization_user', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class, 'user_id')->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Organization::class, 'organization_id')->constrained()->cascadeOnDelete();
            $table->string('role')->default(OrganizationRole::Owner->value);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organization_user');
    }
};
