<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('author_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('body')->nullable();
            // status: 'draft' | 'published' — legacy field, superseded by visibility
            $table->enum('status', ['draft', 'published'])->default('published');
            // visibility: 'public' | 'private' | 'draft' — primary display gate
            $table->string('visibility')->default('public');
            // moderation_status: 'approved' | 'pending' | 'rejected' — set by admin
            $table->string('moderation_status')->default('approved');
            $table->json('tags')->nullable();                   // Array of string tags
            $table->json('modification_history')->nullable();   // Append-only audit log of edits
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_posts');
    }
};
