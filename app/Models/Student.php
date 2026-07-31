<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'department_id',
        'program_id',
        'registration_number',
        'roll_number',
        'first_name',
        'last_name',
        'father_name',
        'email',
        'phone',
        'cnic',
        'gender',
        'dob',
        'address',
        'admission_date',
        'current_semester',
        'status',
    ];

    protected $casts = [
        'dob' => 'date',
        'admission_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function examGrades()
    {
        return $this->hasMany(ExamGrade::class);
    }

    public function feeChallans()
    {
        return $this->hasMany(FeeChallan::class);
    }

    public function getProgramNameAttribute()
    {
        if ($this->program) {
            return $this->program->name;
        }

        if ($this->department_id) {
            $prog = Program::where('department_id', $this->department_id)->first();
            if ($prog) {
                return $prog->name;
            }
        }

        if ($this->department) {
            return $this->department->name;
        }

        return 'Degree Program';
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function attendanceRecords()
    {
        return $this->hasMany(AttendanceRecord::class);
    }

    public function activeCourseOfferings()
    {
        $programId = $this->program_id;
        $deptId = $this->department_id;
        $semesterNum = $this->current_semester ?: 1;

        return CourseOffering::query()
            ->where('status', 'active')
            ->where(function ($q) use ($programId, $deptId) {
                if ($programId) {
                    $q->where('program_id', $programId);
                }
                if ($deptId) {
                    $q->orWhereHas('program', function ($pq) use ($deptId) {
                        $pq->where('department_id', $deptId);
                    });
                }
            })
            ->where(function ($q) use ($semesterNum) {
                $q->where('semester_number', $semesterNum)
                  ->orWhereNull('semester_number');
            })
            ->where(function ($q) {
                $q->whereNull('section_id')
                  ->orWhere('section_id', $this->section_id);
            });
    }

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function offeringGrades()
    {
        return $this->hasMany(CourseOfferingGrade::class, 'student_id');
    }

    public function calculateCgpa()
    {
        $grades = CourseOfferingGrade::with('courseOffering.course')
            ->where('student_id', $this->id)
            ->whereNotNull('grade')
            ->get();

        $totalQualityPoints = 0;
        $totalCreditHours = 0;

        foreach ($grades as $g) {
            $ch = $g->courseOffering->course->credit_hours ?? 3;
            $totalQualityPoints += ($g->gpa_point * $ch);
            $totalCreditHours += $ch;
        }

        return $totalCreditHours > 0 ? number_format($totalQualityPoints / $totalCreditHours, 2) : '0.00';
    }
}
