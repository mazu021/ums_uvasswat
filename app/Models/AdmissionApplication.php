<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdmissionApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_no',
        'program_id',
        'campus_id',
        'applicant_name',
        'cnic',
        'father_name',
        'email',
        'phone',
        'matric_marks',
        'matric_total',
        'inter_marks',
        'inter_total',
        'entry_test_marks',
        'entry_test_total',
        'merit_score',
        'status',
        'remarks',
        'documents',
    ];

    protected $casts = [
        'documents' => 'array',
        'merit_score' => 'float',
        'matric_marks' => 'float',
        'inter_marks' => 'float',
        'entry_test_marks' => 'float',
    ];

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function campus()
    {
        return $this->belongsTo(Campus::class);
    }

    public function calculateMeritScore(): float
    {
        $matricPct = $this->matric_total > 0 ? ($this->matric_marks / $this->matric_total) * 100 : 0;
        $interPct = $this->inter_total > 0 ? ($this->inter_marks / $this->inter_total) * 100 : 0;
        $entryPct = $this->entry_test_total > 0 ? ($this->entry_test_marks / $this->entry_test_total) * 100 : 0;

        // Weight distribution: 20% Matric, 50% Inter (FSc), 30% Entry Test
        return round(($matricPct * 0.20) + ($interPct * 0.50) + ($entryPct * 0.30), 2);
    }
}
