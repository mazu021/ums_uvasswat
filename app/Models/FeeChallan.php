<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeeChallan extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'fee_structure_id',
        'semester',
        'challan_number',
        'issue_date',
        'due_date',
        'total_amount',
        'late_fine_amount',
        'paid_amount',
        'status',
        'paid_at',
        'payment_proof',
        'transaction_reference',
        'payment_notes',
        'rejection_reason',
        'verified_by',
        'verified_at',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'paid_at' => 'datetime',
        'verified_at' => 'datetime',
        'total_amount' => 'decimal:2',
        'late_fine_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function feeStructure()
    {
        return $this->belongsTo(FeeStructure::class);
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
