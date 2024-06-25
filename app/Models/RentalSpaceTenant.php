<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RentalSpaceTenant extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function rentalSpace(){
        return $this->belongsTo(RentalSpace::class, 'rental_space_id');
    }
}
