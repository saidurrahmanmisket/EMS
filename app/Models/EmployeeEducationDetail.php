<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeEducationDetail extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function employee(){
        return $this->belongsTo(Employee::class, 'employee_id');
    }

//    public function getEducationCertificateAttribute($value)
//    {
//        if($value!=null){
//            return url('/').'/'.$value;
//        }else{
//            return $value;
//        }
//    }
}
