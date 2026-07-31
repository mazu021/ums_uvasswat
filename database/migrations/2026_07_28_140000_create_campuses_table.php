<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campuses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('city')->default('Swat');
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->boolean('is_main')->default(false);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });

        // Add campus_id to faculties table if not present
        if (Schema::hasTable('faculties') && !Schema::hasColumn('faculties', 'campus_id')) {
            Schema::table('faculties', function (Blueprint $table) {
                $table->foreignId('campus_id')->nullable()->after('id')->constrained('campuses')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('faculties') && Schema::hasColumn('faculties', 'campus_id')) {
            Schema::table('faculties', function (Blueprint $table) {
                $table->dropForeign(['campus_id']);
                $table->dropColumn('campus_id');
            });
        }
        Schema::dropIfExists('campuses');
    }
};
