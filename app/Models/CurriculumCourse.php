<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CurriculumCourse extends Model
{
    use HasFactory;

    protected $fillable = [
        'curriculum_id',
        'course_id',
        'semester_number',
        'course_type',
        'credit_hours',
        'is_active',
    ];

    public function curriculum()
    {
        return $this->belongsTo(Curriculum::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
