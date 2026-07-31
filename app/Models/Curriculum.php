<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Curriculum extends Model
{
    use HasFactory;

    protected $table = 'curriculums';

    protected $fillable = [
        'program_id',
        'name',
        'code',
        'effective_year',
        'total_semesters',
        'total_credit_hours',
        'status',
    ];

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function curriculumCourses()
    {
        return $this->hasMany(CurriculumCourse::class)->orderBy('semester_number');
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'curriculum_courses')
                    ->withPivot('semester_number', 'course_type', 'credit_hours', 'is_active')
                    ->withTimestamps();
    }

    public function coursesForSemester($semesterNumber)
    {
        return $this->curriculumCourses()->where('semester_number', $semesterNumber)->with('course')->get();
    }
}
