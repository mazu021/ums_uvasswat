<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'department_id',
        'course_code',
        'title',
        'credit_hours',
        'semester',
        'description',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function assignments()
    {
        return $this->hasMany(CourseAssignment::class);
    }

    public function exams()
    {
        return $this->hasMany(Exam::class);
    }

    public function offerings()
    {
        return $this->hasMany(CourseOffering::class);
    }

    public function curriculumCourses()
    {
        return $this->hasMany(CurriculumCourse::class);
    }

    public function curriculums()
    {
        return $this->belongsToMany(Curriculum::class, 'curriculum_courses')
                    ->withPivot('semester_number', 'course_type', 'credit_hours', 'is_active')
                    ->withTimestamps();
    }

    public function getAssignedTeachers()
    {
        return $this->getAssignedTeachersWithDetails()->pluck('name');
    }

    public function getAssignedTeachersWithDetails()
    {
        $teachers = collect();

        foreach ($this->offerings->where('status', 'active') as $offering) {
            if ($offering->teacher) {
                $progTag = $offering->program ? (' (' . ($offering->program->code ?? $offering->program->name) . ')') : '';
                $teachers->push([
                    'name' => $offering->teacher->name . $progTag,
                    'user_id' => $offering->teacher_id,
                    'employee_id' => null,
                    'offering_id' => $offering->id,
                ]);
            }
        }

        foreach ($this->assignments as $asgn) {
            if ($asgn->faculty) {
                $userId = $asgn->faculty->user_id ?? \App\Models\User::where('email', $asgn->faculty->email)->value('id');
                $teachers->push([
                    'name' => $asgn->faculty->full_name,
                    'user_id' => $userId,
                    'employee_id' => $asgn->employee_id,
                ]);
            }
        }

        return $teachers->unique('offering_id')->values();
    }
}
