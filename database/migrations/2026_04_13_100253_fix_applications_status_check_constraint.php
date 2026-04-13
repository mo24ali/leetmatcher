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
        // For PostgreSQL, we need to drop the check constraint created by the enum
        // and add a new one that includes 'in_progress'. 
        // We also handle 'accepted' and 'rejected' which were already there.
        
        if (config('database.default') === 'pgsql') {
            DB::statement('ALTER TABLE applications DROP CONSTRAINT IF EXISTS applications_status_check');
            DB::statement("ALTER TABLE applications ADD CONSTRAINT applications_status_check CHECK (status IN ('pending', 'accepted', 'rejected', 'in_progress'))");
        } else {
            Schema::table('applications', function (Blueprint $table) {
                $table->string('status')->default('pending')->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (config('database.default') === 'pgsql') {
            DB::statement('ALTER TABLE applications DROP CONSTRAINT IF EXISTS applications_status_check');
            DB::statement("ALTER TABLE applications ADD CONSTRAINT applications_status_check CHECK (status IN ('pending', 'accepted', 'rejected'))");
        }
    }
};
