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
        Schema::table('interviews', function (Blueprint $table) {
            $table->string('meeting_link')->nullable()->change();
            $table->text('notes')->nullable()->change();
            $table->integer('score')->default(0)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('interviews', function (Blueprint $table) {
            $table->string('meeting_link')->nullable(false)->change();
            $table->text('notes')->nullable(false)->change();
            $table->integer('score')->default(null)->nullable(false)->change();
        });
    }
};
