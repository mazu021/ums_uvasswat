<?php

use App\Models\CourseOffering;
use App\Models\Student;
use App\Models\AttendanceSession;
use App\Models\AttendanceRecord;
use Carbon\Carbon;

$courseOfferings = CourseOffering::with(['course', 'program'])->get();
$students = Student::all();

if ($courseOfferings->count() > 0 && $students->count() > 0) {
    $topics = [
        'Introduction & Course Orientation',
        'Basic Anatomy & Skeletal Structure',
        'Physiology & Cell Membrane Function',
        'Biochemistry of Metabolic Pathways',
        'Microbiology & Bacterial Cultures Lab',
        'Parasitology Identification & Diagnostics',
        'Animal Nutrition & Feed Formulation',
        'Pharmacology & Drug Administration',
        'Pathology & Disease Diagnostics',
        'Veterinary Surgery Fundamentals & Sterile Techniques'
    ];

    $statuses = ['Present', 'Present', 'Present', 'Present', 'Late', 'Absent', 'Leave'];

    foreach ($courseOfferings as $offering) {
        // Find students enrolled in this offering's department/program or all students
        $enrolled = Student::where('department_id', $offering->program->department_id ?? 1)->get();
        if ($enrolled->count() < 10) {
            $enrolled = $students->take(30);
        }

        // Create 6-8 attendance sessions per course offering over recent dates
        for ($lec = 1; $lec <= 8; $lec++) {
            $date = Carbon::now()->subDays((8 - $lec) * 3)->format('Y-m-d');
            $topic = $topics[($lec - 1) % count($topics)];

            $session = AttendanceSession::create([
                'course_offering_id' => $offering->id,
                'attendance_date' => $date,
                'lecture_number' => $lec,
                'topic' => "Lecture {$lec}: {$topic}",
                'remarks' => 'Regular theory & practical class session.',
                'created_by' => $offering->teacher_id ?? 1,
            ]);

            foreach ($enrolled as $std) {
                AttendanceRecord::create([
                    'attendance_session_id' => $session->id,
                    'student_id' => $std->id,
                    'status' => $statuses[array_rand($statuses)],
                    'remarks' => null,
                ]);
            }
        }
    }
}

echo "ATTENDANCE SEEDING COMPLETE! TOTAL SESSIONS: " . AttendanceSession::count() . " | TOTAL RECORDS: " . AttendanceRecord::count() . "\n";
