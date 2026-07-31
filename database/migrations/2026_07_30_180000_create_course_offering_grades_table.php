<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_offering_grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_offering_id')->constrained('course_offerings')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            
            // Weightage Distribution: Mid (30%), Internal (20%), Final (50%)
            $table->decimal('mid_marks', 5, 2)->nullable();       // Max 30.00
            $table->decimal('internal_marks', 5, 2)->nullable();  // Max 20.00
            $table->decimal('final_marks', 5, 2)->nullable();     // Max 50.00
            $table->decimal('total_marks', 5, 2)->nullable();     // Max 100.00
            
            $table->string('grade', 5)->nullable();               // Letter Grade: A, A-, B+, B, B-, C+, C, D, F
            $table->decimal('gpa_point', 3, 2)->default(0.00);    // 4.00, 3.70, 3.30, 3.00, 2.70, 2.30, 2.00, 1.00, 0.00
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->unique(['course_offering_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_offering_grades');
    }
};
