<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('blog_posts', function (Blueprint $table) {
            $table->text('body')->nullable();
            $table->json('tags')->nullable();
            $table->string('moderation_status')->default('approved'); // approved, pending, rejected
            $table->json('modification_history')->nullable();
            $table->string('visibility')->default('public'); // public, private - status handles draft/published
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blog_posts', function (Blueprint $table) {
            $table->dropColumn(['body', 'tags', 'moderation_status', 'modification_history', 'visibility']);
        });
    }
};
