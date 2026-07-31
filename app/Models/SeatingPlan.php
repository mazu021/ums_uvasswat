<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeatingPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'hall_name',
        'rows',
        'columns',
        'invigilator_name',
        'allocated_students',
    ];

    protected $casts = [
        'allocated_students' => 'array',
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }
}
