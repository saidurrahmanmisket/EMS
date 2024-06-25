<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeTrainingDetail extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function employee(){
        return $this->belongsTo(Employee::class, 'employee_id');
    }

//    public function getTrainingCertificateAttribute($value)
//    {
//        if($value!=null){
//            return url('/').'/'.$value;
//        }else{
//            return $value;
//        }
//    }
}
