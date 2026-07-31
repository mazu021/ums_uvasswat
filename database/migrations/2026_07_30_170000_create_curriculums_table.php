<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('curriculums', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained('programs')->onDelete('cascade');
            $table->string('name'); // e.g. "DPT Study Scheme 2026-2031"
            $table->string('code')->nullable(); // e.g. "SCHEME-DPT-2026"
            $table->integer('effective_year')->default(2026);
            $table->integer('total_semesters')->default(8);
            $table->integer('total_credit_hours')->default(130);
            $table->enum('status', ['active', 'archived', 'draft'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('curriculums');
    }
};
