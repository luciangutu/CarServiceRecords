<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceEntry extends Model
{
    use HasFactory;

    protected $casts = [
        'date' => 'date',
    ];

    protected $fillable = [
        'user_id',
        'date',
        'kilometers',
        'license_plate',
        'service_name',
        'service_action',
        'parts_replaced',
        'cost'
    ];
}