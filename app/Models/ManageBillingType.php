<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManageBillingType extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function company(){
        return $this->belongsTo(Company::class, 'company_id');
    }
    public function manageUtilitySector(){
        return $this->belongsTo(ManageUtilitySector::class, 'manage_utility_sector_id');
    }
    public function manageUtilityBillingSectorInfo()
    {
      return $this->hasMany(ManageUtilityBillingSectorInformation::class);
    }
    public function expenseUtility()
    {
      return $this->hasMany(ExpenseUtility::class);
    }
}
