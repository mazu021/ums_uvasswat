<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_offering_id')->constrained('course_offerings')->onDelete('cascade');
            $table->date('attendance_date');
            $table->unsignedTinyInteger('lecture_number')->default(1);
            $table->string('topic')->nullable();
            $table->text('remarks')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            // Prevents double attendance marking for the same offering, date, and lecture
            $table->unique(['course_offering_id', 'attendance_date', 'lecture_number'], 'unique_offering_lecture_date');
        });

        Schema::create('attendance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_session_id')->constrained('attendance_sessions')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->enum('status', ['Present', 'Absent', 'Leave', 'Late'])->default('Present');
            $table->string('remarks')->nullable();
            $table->timestamps();

            // Prevents duplicate record for the same student in a session
            $table->unique(['attendance_session_id', 'student_id'], 'unique_session_student_record');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_records');
        Schema::dropIfExists('attendance_sessions');
    }
};
