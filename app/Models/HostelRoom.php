<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HostelRoom extends Model
{
    use HasFactory;

    protected $fillable = [
        'hostel_id',
        'room_number',
        'capacity',
        'occupied',
        'monthly_fee',
    ];

    public function hostel()
    {
        return $this->belongsTo(Hostel::class);
    }
}
