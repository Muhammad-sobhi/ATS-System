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
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recruiter_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->string('slug');
            $table->longText('description');
            $table->string('location')->nullable();
            $table->enum('type', ['full_time', 'part_time', 'remote', 'contract'])->default('full_time');
            $table->string('department')->nullable();
            $table->unsignedInteger('slots')->default(1);
            $table->enum('status', ['open', 'closed', 'paused'])->default('open');
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['recruiter_id', 'status']);
            $table->index('slug');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }
};
