<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fee_challans', function (Blueprint $table) {
            $table->string('payment_proof')->nullable()->after('status');
            $table->string('transaction_reference')->nullable()->after('payment_proof');
            $table->text('payment_notes')->nullable()->after('transaction_reference');
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null')->after('payment_notes');
            $table->timestamp('verified_at')->nullable()->after('verified_by');
        });
    }

    public function down(): void
    {
        Schema::table('fee_challans', function (Blueprint $table) {
            $table->dropForeign(['verified_by']);
            $table->dropColumn(['payment_proof', 'transaction_reference', 'payment_notes', 'verified_by', 'verified_at']);
        });
    }
};
