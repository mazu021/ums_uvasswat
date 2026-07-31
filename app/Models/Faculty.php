<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faculty extends Model
{
    use HasFactory;

    protected $fillable = [
        'campus_id',
        'name',
        'code',
        'description',
        'dean_name',
    ];

    public function campus()
    {
        return $this->belongsTo(Campus::class);
    }

    public function departments()
    {
        return $this->hasMany(Department::class);
    }
}
