<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManageBank extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function company(){
        return $this->belongsTo(Company::class, 'company_id');
    }
    public function bankAccounts()
    {
        return $this->hasMany(BankAccounts::class);
    }
}
