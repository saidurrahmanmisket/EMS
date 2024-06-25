<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeOfficialDetail extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function employee(){
        return $this->belongsTo(Employee::class, 'employee_id');
    }
    public function designation(){
        return $this->belongsTo(Designation::class, 'designation_id');
    }
    public function department(){
        return $this->belongsTo(Department::class, 'department_id');
    }
}
