<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleClassTypeWithVehicleSubClassType extends Model
{
    use HasFactory;

    protected $table = 'vehicle_class_types_with_vehicle_sub_class_types';
    protected $fillable = [
        'vehicle_class_type_id',
        'vehicle_sub_class_type_id'
    ];

}
