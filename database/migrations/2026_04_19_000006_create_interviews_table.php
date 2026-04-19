<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('interviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('applications')->cascadeOnDelete();
            $table->timestamp('scheduled_at')->nullable();
            $table->string('meeting_link')->nullable();  // Set when interview is created/updated
            $table->text('notes')->nullable();           // Recruiter notes, optional
            $table->integer('score')->default(0)->nullable();
            // Values: 'scheduled' (default) | 'completed'
            // Completed interviews are NEVER deleted — they are locked via status
            $table->string('status')->default('scheduled');
            $table->timestamp('completed_at')->nullable(); // Set by InterviewController::submitResult
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('interviews');
    }
};
