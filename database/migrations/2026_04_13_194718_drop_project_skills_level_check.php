<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE project_skills DROP CONSTRAINT IF EXISTS project_skills_level_check');
        DB::statement('ALTER TABLE project_skills DROP CONSTRAINT IF EXISTS project_skills_level_check1');
        
        // Also just to be super safe since Laravel might generate different names
        // postgres has no DROP CONSTRAINT IF EXISTS before pg 9.0, but we're on modern so it's fine.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No explicit down needed to restore the broken constraint
    }
};
