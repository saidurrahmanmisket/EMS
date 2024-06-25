<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManageUtilityBillingSectorInformation extends Model


{
    use HasFactory;
    // protected $guarded = [];
    protected $table = 'manage_utility_billing_sector_informations';

    protected $fillable = ['date','company_id','branch_id','rental_space_id','manage_utility_sector_id','meter_number','meter_code','customer_id_number',
    'customer_id_number_code','phone_bill_number','isp_name','manage_billing_type_id','remarks'];

    public function company(){
        return $this->belongsTo(Company::class, 'company_id');
    }
    public function branch(){
        return $this->belongsTo(Branch::class, 'branch_id');
    }
    public function rentalSpace(){
        return $this->belongsTo(RentalSpace::class, 'rental_space_id')->with('RentalSpaceOwner:rental_space_id,owner_name,owner_phone_number');
    }
    public function manageUtilitySector(){
        return $this->belongsTo(ManageUtilitySector::class, 'manage_utility_sector_id');
    }
    public function manageBillingType(){
        return $this->belongsTo(ManageBillingType::class, 'manage_billing_type_id');
    }
    public function manage_utility_billing_sector_information(){
        return $this->hasMany(ExpenseUtility::class, 'manage_utility_billing_sector_informations_id:');
    }
}
