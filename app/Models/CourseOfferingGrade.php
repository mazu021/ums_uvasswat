<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseOfferingGrade extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_offering_id',
        'student_id',
        'mid_marks',
        'internal_marks',
        'final_marks',
        'total_marks',
        'grade',
        'gpa_point',
        'remarks',
    ];

    protected $casts = [
        'mid_marks' => 'float',
        'internal_marks' => 'float',
        'final_marks' => 'float',
        'total_marks' => 'float',
        'gpa_point' => 'float',
    ];

    public function courseOffering()
    {
        return $this->belongsTo(CourseOffering::class, 'course_offering_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    /**
     * Calculate Total, Grade, and GPA Point from component marks.
     * Weightage Distribution: Mid (30%), Internal (20%), Final (50%)
     */
    public static function calculateGradeAndGpa($mid = null, $internal = null, $final = null)
    {
        $hasAnyMark = ($mid !== null || $internal !== null || $final !== null);
        if (!$hasAnyMark) {
            return [
                'total_marks' => null,
                'grade' => null,
                'gpa_point' => 0.00,
            ];
        }

        $total = ($mid ?? 0) + ($internal ?? 0) + ($final ?? 0);
        $total = min(100.00, max(0.00, $total));

        // Grading Scale without minus grades (A+, A, B+, B, C+, C, D, F)
        if ($total >= 90) {
            $grade = 'A+';
            $gpa = 4.00;
        } elseif ($total >= 80) {
            $grade = 'A';
            $gpa = 3.70;
        } elseif ($total >= 75) {
            $grade = 'B+';
            $gpa = 3.30;
        } elseif ($total >= 70) {
            $grade = 'B';
            $gpa = 3.00;
        } elseif ($total >= 65) {
            $grade = 'C+';
            $gpa = 2.50;
        } elseif ($total >= 60) {
            $grade = 'C';
            $gpa = 2.00;
        } elseif ($total >= 50) {
            $grade = 'D';
            $gpa = 1.00;
        } else {
            $grade = 'F';
            $gpa = 0.00;
        }

        return [
            'total_marks' => round($total, 2),
            'grade' => $grade,
            'gpa_point' => $gpa,
        ];
    }
}
