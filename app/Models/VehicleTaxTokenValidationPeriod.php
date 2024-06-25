<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleTaxTokenValidationPeriod extends Model
{
    use HasFactory;

    protected $table = 'vehicle_tax_token_validation_periods';

    protected $fillable = [
        'from',
        'to',
        'image_or_docs',
        'vehicle_id',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

    public function getImageOrDocsAttribute($value)
    {
        if($value!=null) {
            return url('/') . '/' . $value;
        }else{
            return $value;
        }
    }
}
