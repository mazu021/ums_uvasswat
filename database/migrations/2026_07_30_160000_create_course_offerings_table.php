<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_offerings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade'); // User representing teacher/faculty
            $table->foreignId('program_id')->constrained('programs')->onDelete('cascade');
            $table->foreignId('semester_id')->nullable()->constrained('semesters')->onDelete('set null');
            $table->integer('semester_number')->default(1);
            $table->foreignId('batch_id')->constrained('batches')->onDelete('cascade');
            $table->foreignId('section_id')->nullable()->constrained('sections')->onDelete('set null');
            $table->foreignId('academic_session_id')->constrained('academic_sessions')->onDelete('cascade');
            $table->enum('status', ['active', 'completed', 'cancelled'])->default('active');
            $table->timestamps();

            // Composite unique index to avoid identical duplicate offerings
            $table->unique(
                ['course_id', 'program_id', 'batch_id', 'section_id', 'academic_session_id', 'teacher_id'],
                'unique_course_offering_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_offerings');
    }
};
