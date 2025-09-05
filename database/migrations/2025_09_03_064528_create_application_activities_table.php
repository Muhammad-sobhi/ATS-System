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
        Schema::create('application_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('applications')->cascadeOnDelete();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('type', ['status_change', 'comment', 'assignment', 'file_upload', 'note']);
            $table->json('payload')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index('application_id');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('application_activities');
    }
};
