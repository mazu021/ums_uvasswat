<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseOffering extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'teacher_id',
        'program_id',
        'semester_id',
        'semester_number',
        'batch_id',
        'section_id',
        'academic_session_id',
        'status',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function academicSession()
    {
        return $this->belongsTo(AcademicSession::class);
    }

    public function attendanceSessions()
    {
        return $this->hasMany(AttendanceSession::class);
    }

    public function timetables()
    {
        return $this->hasMany(Timetable::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeForTeacher($query, $teacherId)
    {
        return $query->where('teacher_id', $teacherId);
    }

    public function getEnrolledStudents()
    {
        $query = Student::query();

        $deptId = $this->program ? $this->program->department_id : null;

        $query->where(function ($q) use ($deptId) {
            if ($this->program_id) {
                $q->where('program_id', $this->program_id);
            }
            if ($deptId) {
                $q->orWhere('department_id', $deptId);
            }
        });

        $semesterNum = $this->semester_number ?: 1;
        if ($semesterNum) {
            $query->where(function ($q) use ($semesterNum) {
                $q->where('current_semester', $semesterNum)
                  ->orWhereNull('current_semester');
            });
        }

        if ($this->section_id) {
            $query->where('section_id', $this->section_id);
        }

        if ($this->batch_id) {
            $query->where(function ($q) {
                $q->where('batch_id', $this->batch_id)
                  ->orWhereNull('batch_id');
            });
        }

        return $query->where('status', 'active')
            ->orderBy('roll_number')
            ->orderBy('first_name')
            ->get();
    }

    /**
     * Auto-sync CourseAssignment records to CourseOffering for a user
     */
    public static function syncTeacherAssignments(User $user)
    {
        $employee = Employee::where('user_id', $user->id)
            ->orWhere('email', $user->email)
            ->first();

        if (!$employee) {
            $nameParts = explode(' ', $user->name);
            $firstName = $nameParts[0] ?? '';
            $employee = Employee::where('first_name', 'like', "%{$firstName}%")->first();
        }

        if ($employee) {
            if (!$employee->user_id) {
                $employee->update(['user_id' => $user->id]);
            }

            $assignments = CourseAssignment::where('employee_id', $employee->id)->get();

            $academicSession = AcademicSession::where('status', 'active')->first() 
                ?? AcademicSession::firstOrCreate(['code' => 'FALL-2026'], [
                    'name' => 'Fall 2026',
                    'start_date' => '2026-09-01',
                    'end_date' => '2027-01-31',
                    'is_current' => true,
                    'status' => 'active',
                ]);

            foreach ($assignments as $assignment) {
                $course = Course::find($assignment->course_id);
                if (!$course) continue;

                $program = Program::where('department_id', $course->department_id)->first()
                    ?? Program::first();

                $batch = Batch::where('program_id', $program->id)->first()
                    ?? Batch::firstOrCreate(['code' => ($program->code ?? 'GEN') . '-F26'], [
                        'program_id' => $program->id,
                        'academic_session_id' => $academicSession->id,
                        'name' => ($program->name ?? 'Degree') . ' Fall 2026 Batch',
                        'status' => 'active',
                    ]);

                $offeringExists = static::where('course_id', $assignment->course_id)
                    ->where(function ($q) use ($user, $employee) {
                        $q->where('teacher_id', $user->id);
                        if ($employee) {
                            $q->orWhere('teacher_id', $employee->id);
                        }
                    })
                    ->exists();

                if (!$offeringExists) {
                    static::create([
                        'course_id' => $assignment->course_id,
                        'teacher_id' => $user->id,
                        'program_id' => $program->id,
                        'batch_id' => $batch->id,
                        'semester_number' => $assignment->semester ?? 1,
                        'academic_session_id' => $academicSession->id,
                        'status' => 'active',
                    ]);
                }
            }
        }
    }
}
