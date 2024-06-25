<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RentalSpaceThanaPoZip extends Model
{
    use HasFactory;

    protected $guarded = [];
    public function rentalSpaceCity(){
        return $this->belongsTo(RentalSpaceCity::class, 'rental_space_city_id');
    }
}
