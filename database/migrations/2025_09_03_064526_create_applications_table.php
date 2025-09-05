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
            $table->foreignId('job_id')->constrained('jobs')->cascadeOnDelete();
            $table->foreignId('candidate_id')->constrained('candidates')->cascadeOnDelete();

            // Instead of enum status, we use a reference to job_stages
            $table->foreignId('stage_id')->nullable()->constrained('job_stages')->nullOnDelete();

            $table->dateTime('applied_at')->useCurrent();
            $table->enum('source', ['portal', 'email', 'import'])->default('portal');
            $table->longText('resume_snapshot')->nullable();
            $table->longText('cover_letter')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->longText('notes')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['job_id', 'stage_id', 'applied_at']);
            $table->index('candidate_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
