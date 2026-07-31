<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admission_applications', function (Blueprint $table) {
            $table->id();
            $table->string('application_no')->unique();
            $table->foreignId('program_id')->constrained()->onDelete('cascade');
            $table->foreignId('campus_id')->constrained()->onDelete('cascade');
            $table->string('applicant_name');
            $table->string('cnic');
            $table->string('father_name');
            $table->string('email');
            $table->string('phone');
            $table->decimal('matric_marks', 6, 2)->default(0);
            $table->decimal('matric_total', 6, 2)->default(1100);
            $table->decimal('inter_marks', 6, 2)->default(0);
            $table->decimal('inter_total', 6, 2)->default(1100);
            $table->decimal('entry_test_marks', 6, 2)->default(0);
            $table->decimal('entry_test_total', 6, 2)->default(100);
            $table->decimal('merit_score', 5, 2)->default(0);
            $table->enum('status', ['submitted', 'under_review', 'fee_pending', 'approved', 'enrolled', 'rejected'])->default('submitted');
            $table->text('remarks')->nullable();
            $table->json('documents')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admission_applications');
    }
};
