<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
    public function official_detail()
    {
        return $this->hasOne(EmployeeOfficialDetail::class);
    }
    public function designation()
    {
        return $this->belongsToMany(Designation::class, EmployeeOfficialDetail::class, 'employee_id', 'designation_id');
    }
    public function department()
    {
        return $this->belongsToMany(Department::class, EmployeeOfficialDetail::class, 'employee_id', 'department_id');
    }
    public function education()
    {
        return $this->hasMany(EmployeeEducationDetail::class);
    }
    public function experience()
    {
        return $this->hasMany(EmployeeExperienceDetail::class);
    }
    public function training()
    {
        return $this->hasMany(EmployeeTrainingDetail::class);
    }
    public function promotion()
    {
        return $this->hasMany(EmployeePromotionDetail::class);
    }
    public function previous_designation()
    {
        return $this->belongsToMany(Designation::class, EmployeePromotionDetail::class, 'employee_id', 'previous_designation_id');
    }
    public function promoted_designation()
    {
        return $this->belongsToMany(Designation::class, EmployeePromotionDetail::class, 'employee_id', 'promoted_designation_id');
    }
    public function reference()
    {
        return $this->hasMany(EmployeeReferenceDetail::class);
    }

    //    public function getImageAttribute($value)
    //    {
    //        return url('/').'/'.$value;
    //    }
    //    public function getNidImageAttribute($value)
    //    {
    //        return url('/').'/'.$value;
    //    }

    public function expense_information_related_to_employee()
    {
        return $this->hasOne(ExpenseInformationRelatedToEmployee::class);
    }

    // public function salary_for_every_month_for_every_employee()
    // {
    //     return $this->hasMany(SalaryForEveryMonthForEveryEmployee::class);
    // }
    public function expense_employee_regular(){
        return $this->hasMany(ExpenseEmployeeRegular::class);
    }
    //Expense Employee
    public function expenseEmployee()
    {
        return $this->hasMany(ExpenseEmployee::class);
    }
}
