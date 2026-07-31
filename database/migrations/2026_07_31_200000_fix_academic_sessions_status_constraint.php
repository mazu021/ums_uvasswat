<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('CREATE TABLE IF NOT EXISTS academic_sessions_dg_tmp (id INTEGER PRIMARY KEY AUTOINCREMENT, name VARCHAR NOT NULL, code VARCHAR UNIQUE, start_date DATE, end_date DATE, is_current TINYINT(1) DEFAULT 0, status VARCHAR DEFAULT "active", created_at DATETIME, updated_at DATETIME)');
            DB::statement('INSERT OR IGNORE INTO academic_sessions_dg_tmp SELECT id, name, code, start_date, end_date, is_current, status, created_at, updated_at FROM academic_sessions');
            DB::statement('DROP TABLE IF EXISTS academic_sessions');
            DB::statement('ALTER TABLE academic_sessions_dg_tmp RENAME TO academic_sessions');
        } else {
            Schema::table('academic_sessions', function (Blueprint $table) {
                $table->string('status')->default('active')->change();
            });
        }
    }

    public function down(): void
    {
    }
};
