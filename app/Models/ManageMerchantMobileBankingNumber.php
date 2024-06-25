<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManageMerchantMobileBankingNumber extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
    public function manageMerchantMobileBankingName()
    {
        return $this->belongsTo(ManageMerchantMobileBankingName::class, 'manage_merchant_mobile_banking_name_id');
    }
}
