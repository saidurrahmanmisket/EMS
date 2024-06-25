<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RentalSpaceCity extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function rentalSpaceThanaPoZip(){
        return $this->hasMany(RentalSpaceThanaPoZip::class);
    }
}
