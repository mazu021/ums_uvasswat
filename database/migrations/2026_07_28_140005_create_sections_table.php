<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('semester_id')->constrained('semesters')->onDelete('cascade');
            $table->string('name'); // e.g. Section A
            $table->integer('max_capacity')->default(50);
            $table->foreignId('coordinator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Add relationships to courses, students if needed
        if (Schema::hasTable('courses') && !Schema::hasColumn('courses', 'program_id')) {
            Schema::table('courses', function (Blueprint $table) {
                $table->foreignId('program_id')->nullable()->after('department_id')->constrained('programs')->nullOnDelete();
                $table->foreignId('semester_id')->nullable()->after('program_id')->constrained('semesters')->nullOnDelete();
            });
        }

        if (Schema::hasTable('students') && !Schema::hasColumn('students', 'program_id')) {
            Schema::table('students', function (Blueprint $table) {
                $table->foreignId('campus_id')->nullable()->after('department_id')->constrained('campuses')->nullOnDelete();
                $table->foreignId('program_id')->nullable()->after('campus_id')->constrained('programs')->nullOnDelete();
                $table->foreignId('batch_id')->nullable()->after('program_id')->constrained('batches')->nullOnDelete();
                $table->foreignId('semester_id')->nullable()->after('batch_id')->constrained('semesters')->nullOnDelete();
                $table->foreignId('section_id')->nullable()->after('semester_id')->constrained('sections')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('students')) {
            Schema::table('students', function (Blueprint $table) {
                $table->dropForeign(['campus_id']);
                $table->dropForeign(['program_id']);
                $table->dropForeign(['batch_id']);
                $table->dropForeign(['semester_id']);
                $table->dropForeign(['section_id']);
                $table->dropColumn(['campus_id', 'program_id', 'batch_id', 'semester_id', 'section_id']);
            });
        }

        if (Schema::hasTable('courses')) {
            Schema::table('courses', function (Blueprint $table) {
                $table->dropForeign(['program_id']);
                $table->dropForeign(['semester_id']);
                $table->dropColumn(['program_id', 'semester_id']);
            });
        }

        Schema::dropIfExists('sections');
    }
};
