<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }
    public function driver_education(){
        return $this->hasMany(DriverEducationDetail::class);
    }
    public function driver_experience(){
        return $this->hasMany(DriverExperienceDetail::class);
    }
    public function driver_increment(){
        return $this->hasMany(DriverIncrementDetail::class);
    }
    public function driver_license(){
        return $this->hasMany(DriverLicenseDetail::class);
    }
    public function driver_reference(){
        return $this->hasMany(DriverReferenceDetail::class);
    }
    public function company(){
        return $this->belongsTo(Company::class, 'company_id');
    }
    public function branch(){
        return $this->belongsTo(Branch::class, 'branch_id');
    }
}
