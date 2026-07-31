<?php

use App\Models\Employee;
use App\Models\LeaveApplication;
use App\Models\LeaveType;

$employees = Employee::all();
$leaveTypes = LeaveType::all();

if ($employees->count() > 0 && $leaveTypes->count() > 0) {
    $reasons = [
        'Attending international research conference on Veterinary Medicine.',
        'Urgent family emergency and personal domestic matters.',
        'Medical leave due to seasonal flu and fever prescribed rest.',
        'Annual summer vacation leave for family travel.',
        'Attending professional development workshop on curriculum enhancement.',
        'Short medical leave for dental procedure and recovery.',
        'Personal leave for home maintenance supervision.',
        'Attending university senate meeting and academic council committee duties.'
    ];

    $statuses = ['approved', 'approved', 'approved', 'pending', 'rejected', 'approved'];

    foreach ($employees as $emp) {
        for ($i = 0; $i < 5; $i++) {
            $year = [2024, 2025, 2026][rand(0, 2)];
            $month = rand(1, 12);
            $day = rand(1, 20);
            $startDate = sprintf('%04d-%02d-%02d', $year, $month, $day);
            $duration = rand(1, 5);
            $endDate = date('Y-m-d', strtotime($startDate . ' + ' . ($duration - 1) . ' days'));
            $status = $statuses[array_rand($statuses)];

            LeaveApplication::create([
                'employee_id' => $emp->id,
                'leave_type_id' => $leaveTypes->random()->id,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'total_days' => $duration,
                'reason' => $reasons[array_rand($reasons)],
                'status' => $status,
                'approved_by' => $status !== 'pending' ? 1 : null,
            ]);
        }
    }
}

echo "LEAVE SEEDING COMPLETED. TOTAL LEAVES IN DB: " . LeaveApplication::count() . "\n";
