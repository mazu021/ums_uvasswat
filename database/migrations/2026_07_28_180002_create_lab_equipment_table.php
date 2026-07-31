<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lab_equipment', function (Blueprint $table) {
            $table->id();
            $table->string('asset_code')->unique();
            $table->string('name');
            $table->string('category')->default('Diagnostic');
            $table->foreignId('department_id')->nullable()->constrained()->onDelete('set null');
            $table->integer('quantity')->default(1);
            $table->enum('condition', ['working', 'under_maintenance', 'decommissioned'])->default('working');
            $table->date('last_calibrated_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lab_equipment');
    }
};
