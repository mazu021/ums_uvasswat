<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeeStructure extends Model
{
    use HasFactory;

    protected $fillable = [
        'department_id',
        'program_id',
        'academic_session_id',
        'semester',
        'tuition_fee',
        'admission_fee',
        'examination_fee',
        'library_fee',
        'other_charges',
        'late_fee_fine',
        'total_amount',
    ];

    protected $casts = [
        'tuition_fee' => 'decimal:2',
        'admission_fee' => 'decimal:2',
        'examination_fee' => 'decimal:2',
        'library_fee' => 'decimal:2',
        'other_charges' => 'decimal:2',
        'late_fee_fine' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function academicSession()
    {
        return $this->belongsTo(AcademicSession::class);
    }

    public function feeChallans()
    {
        return $this->hasMany(FeeChallan::class);
    }
}
