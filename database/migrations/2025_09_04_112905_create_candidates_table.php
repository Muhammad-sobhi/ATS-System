<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('candidates', function (Blueprint $table) {
            // 👇 id matches users.id (NOT auto increment)
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('name');
            // Candidate profile fields
            $table->string('phone')->nullable();
            $table->string('email')->unique();
            $table->string('resume_url')->nullable();
            $table->string('linkedin_url')->nullable();
            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('candidates');
    }
};
