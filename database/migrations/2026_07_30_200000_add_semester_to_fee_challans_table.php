<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fee_challans', function (Blueprint $table) {
            if (!Schema::hasColumn('fee_challans', 'semester')) {
                $table->integer('semester')->default(1)->after('fee_structure_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('fee_challans', function (Blueprint $table) {
            $table->dropColumn('semester');
        });
    }
};
