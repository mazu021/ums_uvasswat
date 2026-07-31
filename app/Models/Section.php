<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;

    protected $fillable = [
        'semester_id',
        'name',
        'max_capacity',
        'coordinator_id',
    ];

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function coordinator()
    {
        return $this->belongsTo(User::class, 'coordinator_id');
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }
}
