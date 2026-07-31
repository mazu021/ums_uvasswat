<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransportRoute extends Model
{
    use HasFactory;

    protected $fillable = [
        'route_name',
        'vehicle_number',
        'driver_name',
        'driver_phone',
        'monthly_fee',
        'stops',
    ];

    protected $casts = [
        'stops' => 'array',
    ];
}
