<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RentalSpaceDocument extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function rental_space(){
        return $this->belongsTo(RentalSpace::class, 'rental_space_id');
    }
}
