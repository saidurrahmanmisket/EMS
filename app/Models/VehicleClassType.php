<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleClassType extends Model
{
    use HasFactory;

    protected $table = 'vehicle_class_types';
    protected $fillable = [
        'name'
    ];

}
