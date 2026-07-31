<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scholarships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('sponsor_name')->default('UVAS Swat Financial Aid');
            $table->decimal('discount_percentage', 5, 2)->default(0);
            $table->decimal('awarded_amount', 10, 2)->default(0);
            $table->enum('status', ['applied', 'under_review', 'awarded', 'rejected'])->default('applied');
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scholarships');
    }
};
