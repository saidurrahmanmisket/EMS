<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehiclePreviousOwnerOrSellerInformation extends Model
{
    use HasFactory;

    protected $table = 'vehicle_previous_owner_or_seller_informations';

    protected $fillable = [
        'previous_owner_address',
        'phone_number',
        'image_or_docs',
        'remark',
        'vehicle_previous_owner_or_seller_id',
        'vehicle_dealer_or_seller_showroom_id',
        'vehicle_id',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

    public function vehicle_previous_owner_or_seller()
    {
        return $this->belongsTo(VehiclePreviousOwnerOrSeller::class, 'vehicle_previous_owner_or_seller_id');
    }

    public function vehicle_dealer_or_seller_showroom()
    {
        return $this->belongsTo(VehicleDealerOrShowroom::class, 'vehicle_dealer_or_seller_showroom_id');
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
