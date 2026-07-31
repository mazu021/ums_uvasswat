<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    use HasFactory;

    protected $fillable = [
        'program_id',
        'academic_session_id',
        'name',
        'code',
        'batch_advisor_id',
        'status',
    ];

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function academicSession()
    {
        return $this->belongsTo(AcademicSession::class);
    }

    public function batchAdvisor()
    {
        return $this->belongsTo(User::class, 'batch_advisor_id');
    }

    public function semesters()
    {
        return $this->hasMany(Semester::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }
}
