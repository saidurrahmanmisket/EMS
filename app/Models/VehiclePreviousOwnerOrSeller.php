<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehiclePreviousOwnerOrSeller extends Model
{
    use HasFactory;

    protected $table = 'vehicle_previous_owners_or_sellers';

    protected $fillable = [
        'seller_code',
        'seller_name',
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
