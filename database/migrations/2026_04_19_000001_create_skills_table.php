<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('skills', function (Blueprint $table) {
            $table->id();
            // user_id is a legacy column kept for backward compatibility.
            // Skills are now a shared canonical registry; user_id is always NULL in practice.
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name')->unique(); // Enforces one canonical entry per skill name
            $table->string('proficiency')->nullable(); // Legacy column, not used in new logic
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('skills');
    }
};
