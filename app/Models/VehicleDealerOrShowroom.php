<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleDealerOrShowroom extends Model
{
    use HasFactory;

    protected $table = 'vehicle_dealer_or_showrooms';

    protected $fillable = [
        'showroom_code',
        'showroom_name',
        'image_or_docs',
        'company_id',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
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
