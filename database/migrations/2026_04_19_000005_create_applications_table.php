<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            // Fixed: FK correctly targets 'projects', not 'users' (bug in original migration)
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            // String (not ENUM) so PostgreSQL allows adding new statuses without DDL ALTER
            // Valid values: pending | in_progress | accepted | rejected
            $table->string('status')->default('pending');
            $table->text('cover_letter')->nullable();
            // Prevents a student from applying to the same project twice
            $table->unique(['student_id', 'project_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
