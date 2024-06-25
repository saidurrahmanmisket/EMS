<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleSubClassType extends Model
{
    use HasFactory;

    protected $table = 'vehicle_sub_class_types';
    protected $fillable = [
        'name'
    ];

}
