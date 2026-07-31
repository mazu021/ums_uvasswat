<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'days_allowed',
        'description',
    ];

    public function leaveApplications()
    {
        return $this->hasMany(LeaveApplication::class);
    }
}
