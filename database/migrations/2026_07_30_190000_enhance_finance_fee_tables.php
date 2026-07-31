<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fee_structures', function (Blueprint $table) {
            if (!Schema::hasColumn('fee_structures', 'program_id')) {
                $table->foreignId('program_id')->nullable()->constrained('programs')->onDelete('cascade')->after('department_id');
            }
            if (!Schema::hasColumn('fee_structures', 'late_fee_fine')) {
                $table->decimal('late_fee_fine', 10, 2)->default(0.00)->after('other_charges');
            }
            if (!Schema::hasColumn('fee_structures', 'academic_session_id')) {
                $table->foreignId('academic_session_id')->nullable()->constrained('academic_sessions')->onDelete('set null')->after('program_id');
            }
        });

        Schema::table('fee_challans', function (Blueprint $table) {
            if (!Schema::hasColumn('fee_challans', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('payment_notes');
            }
            if (!Schema::hasColumn('fee_challans', 'late_fine_amount')) {
                $table->decimal('late_fine_amount', 10, 2)->default(0.00)->after('total_amount');
            }
        });
    }

    public function down(): void
    {
        Schema::table('fee_structures', function (Blueprint $table) {
            $table->dropColumn(['program_id', 'late_fee_fine', 'academic_session_id']);
        });

        Schema::table('fee_challans', function (Blueprint $table) {
            $table->dropColumn(['rejection_reason', 'late_fine_amount']);
        });
    }
};
