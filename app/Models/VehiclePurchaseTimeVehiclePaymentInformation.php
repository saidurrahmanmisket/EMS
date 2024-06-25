<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehiclePurchaseTimeVehiclePaymentInformation extends Model
{
    use HasFactory;

    protected $table = 'vehicle_purchase_time_vehicle_payment_informations';

    protected $fillable = [
        'vehicle_price',
        'down_payment',
        'installment_number_id',
        'first_payment_date',
        'provable_installment_finish_date',
        'installment_amount',
        'total_due',
        'image_or_docs',
        'remark',
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

    public function getInstallmentNumberIdAttribute($value)
    {
        if($value) {
            return Vehicle::get_id_with_value($value, Vehicle::$installment_number_array[$value]);
        }
    }
}
