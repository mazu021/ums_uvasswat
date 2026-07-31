<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('curriculum_courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('curriculum_id')->constrained('curriculums')->onDelete('cascade');
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->integer('semester_number')->default(1);
            $table->enum('course_type', ['core', 'elective', 'general', 'lab', 'project'])->default('core');
            $table->integer('credit_hours')->default(3);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['curriculum_id', 'course_id', 'semester_number'], 'unique_curriculum_course');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('curriculum_courses');
    }
};
