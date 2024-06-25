<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleRegistrationInformation extends Model
{
    use HasFactory;

    protected $table = 'vehicle_registration_informations';

    protected $fillable = [
        'registration_date',
        'vehicle_buying_condition_id',
        'tin_certificate',
        'vehicle_registration_type_id',
        'registration_fee',
        'registration_invoice_number',
        'sale_certificate',
        'invoice_for_payment_of_vat',
        'vat_payment_receipt',
        'musac_1',
        'musac_11_a_or_vat',
        'body_vat_invoice',
        'receipt_of_deposit_of_applicable_registration_fee',
        'registered_new_owner_name',
        'new_owner_phone_number',
        'new_owner_nid_number',
        'ownership_transfer_fee',
        'chassis_number',
        'engine_no',
        'model_no',
        'tire_size',
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
        if ($value != null) {
            return url('/') . '/' . $value;
        } else {
            return $value;
        }
    }

    public function getVehicleBuyingConditionIdAttribute($value)
    {
        if ($value) {
            return Vehicle::get_id_with_value($value, Vehicle::$vehicle_buying_condition_array[$value]);
        }
    }

    public function getVehicleRegistrationTypeIdAttribute($value)
    {
        if ($value) {
            return Vehicle::get_id_with_value($value, Vehicle::$registration_type_array[$value]);
        }
    }

}
