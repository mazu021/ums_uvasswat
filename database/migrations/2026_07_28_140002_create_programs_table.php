<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained('departments')->onDelete('cascade');
            $table->string('name'); // e.g. Doctor of Veterinary Medicine (DVM)
            $table->string('code')->unique(); // e.g. DVM
            $table->string('degree_level')->default('Undergraduate'); // Undergraduate, Postgraduate, Doctorate
            $table->integer('duration_years')->default(4);
            $table->integer('total_semesters')->default(8);
            $table->integer('total_credit_hours')->default(130);
            $table->text('description')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('programs');
    }
};
