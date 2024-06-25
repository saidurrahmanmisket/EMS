<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RentalSpace extends Model
{
    use HasFactory;

    protected $guarded = [];


    public function company(){
        return $this->belongsTo(Company::class, 'company_id');
    }
    public function branch(){
        return $this->belongsTo(Branch::class, 'branch_id');
    }
    public function rentalSpaceOwner(){
        return $this->hasOne(RentalSpaceOwner::class);
    }
    public function rentalSpaceTenant(){
        return $this->hasOne(RentalSpaceTenant::class);
    }
    public function rentalSpaceFee(){
        return $this->hasOne(RentalSpaceFee::class);
    }
    public function rentalSpaceParkingFee(){
        return $this->hasOne(RentalSpaceParkingFee::class);
    }
    public function rentalSpaceThanaPoZip(){
        return $this->belongsTo(RentalSpaceThanaPoZip::class, 'rental_space_thana_po_zip_id');
    }
    public function rentalSpaceCity(){
        return $this->belongsTo(RentalSpaceCity::class, 'rental_space_city_id');
    }
    public function manageUtilityBillingSectorInfo(){
        return $this->hasOne(ManageUtilityBillingSectorInformation::class);
    }
    public function owner()
    {
        return $this->belongsTo(RentalSpaceOwner::class, 'rental_space_owner_id');
    }
    public function expenseRental(){
        return $this->hasMany(ExpenseRental::class);
    }
    public function expenseUtility(){
        return $this->hasMany(ExpenseUtility::class);
    }

}
