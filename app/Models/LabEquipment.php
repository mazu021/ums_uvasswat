<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LabEquipment extends Model
{
    use HasFactory;

    protected $table = 'lab_equipment';

    protected $fillable = [
        'asset_code',
        'name',
        'category',
        'department_id',
        'quantity',
        'condition',
        'last_calibrated_at',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
