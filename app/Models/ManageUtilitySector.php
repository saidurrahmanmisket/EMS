<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManageUtilitySector extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function company(){
        return $this->belongsTo(Company::class, 'company_id');
    }
    public function manageBillingType(){
        return $this->hasMany(ManageBillingType::class);
    }
    public function manageUtilityBillingSector()
    {
      return $this->hasMany(ManageUtilityBillingSectorInformation::class);
    }

    public function manageUtilityBillingSectorInfo()
    {
        return $this->hasMany(ManageUtilityBillingSectorInformation::class);
    }
}
