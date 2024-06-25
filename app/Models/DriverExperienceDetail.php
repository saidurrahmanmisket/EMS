<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverExperienceDetail extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function driver(){
        return $this->belongsTo(Driver::class, 'driver_id');
    }

}
