<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_offering_id',
        'attendance_date',
        'lecture_number',
        'topic',
        'remarks',
        'created_by',
    ];

    protected $casts = [
        'attendance_date' => 'date',
    ];

    public function courseOffering()
    {
        return $this->belongsTo(CourseOffering::class);
    }

    public function records()
    {
        return $this->hasMany(AttendanceRecord::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
