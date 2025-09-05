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
        Schema::create('job_tags', function (Blueprint $table) {
            $table->foreignId('job_id')->constrained('jobs')->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained('tags')->cascadeOnDelete();
            $table->primary(['job_id', 'tag_id']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('job_tags');
    }
};
