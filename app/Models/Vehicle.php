<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $table = 'vehicles';
    protected $fillable = [
        'date',
        'company_id',
        'branch_id',
        'vehicle_code',
        'vehicle_name',
        'vehicle_type_id',
        'vehicle_class_type_id',
        'vehicle_sub_class_type_id',
        'vehicle_color_id',
        'vehicle_brand_name_id',
        'vehicle_cc',
        'vehicle_class_letter_id',
        'vehicle_no',
        'weight_capacity',
        'lifting_capacity',
        'vehicle_fuel_type_id',
        'manufacturer_year',
        'purchase_date_mileage',
        'registration_date',
        'image_or_docs',
        'remark'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function vehicle_class_type()
    {
        return $this->belongsTo(VehicleClassType::class, 'vehicle_class_type_id');
    }

    public function vehicle_sub_class_type()
    {
        return $this->belongsTo(VehicleSubClassType::class, 'vehicle_sub_class_type_id');
    }

    public function vehicle_previous_owner_or_seller_information()
    {
        return $this->hasOne(VehiclePreviousOwnerOrSellerInformation::class, 'vehicle_id');
    }

    public function vehicle_purchase_time_vehicle_payment_information()
    {
        return $this->hasOne(VehiclePurchaseTimeVehiclePaymentInformation::class, 'vehicle_id');
    }

    public function vehicle_registration_information()
    {
        return $this->hasOne(VehicleRegistrationInformation::class, 'vehicle_id');
    }

    public function vehicle_tax_token_validation_periods()
    {
        return $this->hasMany(VehicleTaxTokenValidationPeriod::class);
    }

    public function vehicle_fitness_validation_periods()
    {
        return $this->hasMany(VehicleFitnessValidationPeriod::class);
    }

    public function vehicle_insurance_validation_periods()
    {
        return $this->hasMany(VehicleInsuranceValidationPeriod::class);
    }

    public function vehicle_free_servicing_validation_periods()
    {
        return $this->hasMany(VehicleFreeServicingValidationPeriod::class);
    }


//
//    public function designation()
//    {
//        return $this->belongsToMany(Designation::class, EmployeeOfficialDetail::class, 'employee_id', 'designation_id');
//    }
//

    public function getImageOrDocsAttribute($value)
    {
        if ($value != null) {
            return url('/') . '/' . $value;
        } else {
            return $value;
        }
    }

    public static function get_id_with_value($value, $value_from_array)
    {
        if ($value != null) {
            return [
                'id' => $value,
                'value' => $value_from_array
            ];
        } else {
            return $value;
        }
    }

    public function getVehicleTypeIdAttribute($value)
    {
        if ($value) {
            return self::get_id_with_value($value, self::$vehicle_type_array[$value]);
        }
    }

    public function getVehicleColorIdAttribute($value)
    {
        if ($value) {
            return self::get_id_with_value($value, self::$vehicle_color_array[$value]);
        }
    }

    public function getVehicleBrandNameIdAttribute($value)
    {
        if ($value) {
            return self::get_id_with_value($value, self::$brand_name_array[$value]);
        }
    }

    public function getVehicleClassLetterIdAttribute($value)
    {
        if ($value) {
            return self::get_id_with_value($value, self::$vehicle_class_letter_array[$value]);
        }
    }

    public function getVehicleFuelTypeIdAttribute($value)
    {
        if ($value) {
            return self::get_id_with_value($value, self::$vehicle_fuel_type_array[$value]);
        }
    }

    static $vehicle_type_array = [
        '1' => 'private',
        '2' => 'govt'
    ];

    static $vehicle_color_array = [
        '1' => 'crimson red',
        '2' => 'metal white',
        '3' => 'black'
    ];

    static $brand_name_array = [
        '1' => 'bmw',
        '2' => 'yamaha',
        '3' => 'tyota'
    ];

    static $vehicle_class_letter_array = [
        '1' => 'ক',
        '2' => 'খ',
        '3' => 'গ',
        '4' => 'ঘ',
        '5' => 'ঙ '
    ];

    static $vehicle_fuel_type_array = [
        '1' => 'octane',
        '2' => 'petrol',
        '3' => 'diesel'
    ];

    static $installment_number_array = [
        '1' => '1',
        '2' => '2',
        '3' => '3',
        '4' => '4',
        '5' => '5',
        '6' => '6',
        '7' => '7',
        '8' => '8',
        '9' => '9',
        '10' => '10',
        '11' => '11',
        '12' => '12',
        '13' => '13',
        '14' => '14',
        '15' => '15',
        '16' => '16',
        '17' => '17',
        '18' => '18',
        '19' => '19',
        '20' => '20',
    ];


    static $vehicle_buying_condition_array = [
        '1' => 'New',
        '2' => 'Recondition',
    ];

    static $registration_type_array = [
        '1' => 'Registration Type 1',
        '2' => 'Registration Type 2',
    ];


}
