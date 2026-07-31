<?php

namespace Database\Seeders;

use App\Models\AcademicSession;
use App\Models\FeeStructure;
use App\Models\Program;
use Illuminate\Database\Seeder;

class FeeStructureSessionSeeder extends Seeder
{
    public function run(): void
    {
        // Delete old/unassigned fee structure records to clean database
        FeeStructure::query()->delete();

        // 1. Create / Update Academic Sessions
        $s2022 = AcademicSession::updateOrCreate(
            ['code' => 'SESSION-2022-2023'],
            [
                'name' => 'Session 2022 - 2023',
                'start_date' => '2022-09-01',
                'end_date' => '2023-01-31',
                'is_current' => false,
                'status' => 'active',
            ]
        );

        $s2023 = AcademicSession::updateOrCreate(
            ['code' => 'SESSION-2023-2024'],
            [
                'name' => 'Session 2023 - 2024',
                'start_date' => '2023-09-01',
                'end_date' => '2024-01-31',
                'is_current' => false,
                'status' => 'active',
            ]
        );

        $s2024 = AcademicSession::updateOrCreate(
            ['code' => 'SESSION-2024-2025'],
            [
                'name' => 'Session 2024 - 2025',
                'start_date' => '2024-09-01',
                'end_date' => '2025-01-31',
                'is_current' => false,
                'status' => 'active',
            ]
        );

        $s2025 = AcademicSession::updateOrCreate(
            ['code' => 'SESSION-2025-2026'],
            [
                'name' => 'Session 2025 - 2026',
                'start_date' => '2025-09-01',
                'end_date' => '2026-01-31',
                'is_current' => false,
                'status' => 'active',
            ]
        );

        $s2026 = AcademicSession::updateOrCreate(
            ['code' => 'SESSION-2026-2027'],
            [
                'name' => 'Session 2026 - 2027',
                'start_date' => '2026-09-01',
                'end_date' => '2027-01-31',
                'is_current' => true,
                'status' => 'active',
            ]
        );

        // Fetch Programs
        $dvmOpen = Program::where('code', 'DVM-OPEN')->first();
        $dvmSelf = Program::where('code', 'DVM-SELF')->first();

        // --- 1. Session 2024-2025 Fee Structures (Applies to All Semesters 1-10) ---
        // DVM Open = 35,000
        if ($dvmOpen) {
            FeeStructure::create([
                'academic_session_id' => $s2024->id,
                'program_id' => $dvmOpen->id,
                'department_id' => $dvmOpen->department_id,
                'semester' => 0, // 0 = All Semesters
                'tuition_fee' => 35000,
                'admission_fee' => 0,
                'examination_fee' => 0,
                'library_fee' => 0,
                'other_charges' => 0,
                'total_amount' => 35000,
                'late_fee_fine' => 500,
            ]);
        }

        // DVM Self = 70,000
        if ($dvmSelf) {
            FeeStructure::create([
                'academic_session_id' => $s2024->id,
                'program_id' => $dvmSelf->id,
                'department_id' => $dvmSelf->department_id,
                'semester' => 0,
                'tuition_fee' => 70000,
                'admission_fee' => 0,
                'examination_fee' => 0,
                'library_fee' => 0,
                'other_charges' => 0,
                'total_amount' => 70000,
                'late_fee_fine' => 500,
            ]);
        }

        // --- 2. Session 2025-2026 Fee Structures (Applies to All Semesters 1-10) ---
        // DVM Open = 45,000
        if ($dvmOpen) {
            FeeStructure::create([
                'academic_session_id' => $s2025->id,
                'program_id' => $dvmOpen->id,
                'department_id' => $dvmOpen->department_id,
                'semester' => 0,
                'tuition_fee' => 45000,
                'admission_fee' => 0,
                'examination_fee' => 0,
                'library_fee' => 0,
                'other_charges' => 0,
                'total_amount' => 45000,
                'late_fee_fine' => 500,
            ]);
        }

        // DVM Self = 90,000
        if ($dvmSelf) {
            FeeStructure::create([
                'academic_session_id' => $s2025->id,
                'program_id' => $dvmSelf->id,
                'department_id' => $dvmSelf->department_id,
                'semester' => 0,
                'tuition_fee' => 90000,
                'admission_fee' => 0,
                'examination_fee' => 0,
                'library_fee' => 0,
                'other_charges' => 0,
                'total_amount' => 90000,
                'late_fee_fine' => 500,
            ]);
        }

        // All other ACTIVE programs for Session 2025-2026 = Rs. 18,000
        $excludedIds = array_filter([$dvmOpen ? $dvmOpen->id : null, $dvmSelf ? $dvmSelf->id : null]);
        $otherProgs = Program::where('status', 'active')->whereNotIn('id', $excludedIds)->get();

        foreach ($otherProgs as $p) {
            FeeStructure::create([
                'academic_session_id' => $s2025->id,
                'program_id' => $p->id,
                'department_id' => $p->department_id,
                'semester' => 0, // 0 = All Semesters
                'tuition_fee' => 18000,
                'admission_fee' => 0,
                'examination_fee' => 0,
                'library_fee' => 0,
                'other_charges' => 0,
                'total_amount' => 18000,
                'late_fee_fine' => 500,
            ]);
        }

        // --- 3. Session 2026-2027 Fee Structures (All ACTIVE Programs Rs. 20,000 for All Semesters) ---
        $allActiveProgs = Program::where('status', 'active')->get();
        foreach ($allActiveProgs as $p) {
            FeeStructure::create([
                'academic_session_id' => $s2026->id,
                'program_id' => $p->id,
                'department_id' => $p->department_id,
                'semester' => 0, // 0 = All Semesters
                'tuition_fee' => 20000,
                'admission_fee' => 0,
                'examination_fee' => 0,
                'library_fee' => 0,
                'other_charges' => 0,
                'total_amount' => 20000,
                'late_fee_fine' => 500,
            ]);
        }
    }
}
