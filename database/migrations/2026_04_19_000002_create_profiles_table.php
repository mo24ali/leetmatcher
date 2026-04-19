<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('avatar_url')->nullable();       // Relative path under public disk (avatars/)
            $table->text('bio')->nullable();
            $table->string('education_level')->nullable();
            $table->string('portfolio_url')->nullable();
            $table->string('cv_path')->nullable();          // Path under local disk (cvs/) — NOT public
            $table->string('cv_score')->nullable();         // Profile completeness 0-100 (set by CvScoringService)
            $table->string('availability')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
