<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Timetable extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_offering_id',
        'day_of_week',
        'start_time',
        'end_time',
        'room_number',
        'building',
    ];

    public function courseOffering()
    {
        return $this->belongsTo(CourseOffering::class);
    }
}
