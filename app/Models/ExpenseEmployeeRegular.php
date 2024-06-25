<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseEmployeeRegular extends Model
{
    use HasFactory;

    protected $table = 'expense_employee_regulars';

    protected $fillable = [
        'date',
        'company_id',
        'branch_id',
        'employee_expense_code',
        'expense_category_id',
        'expense_sector_id',
        'employee_expense_category_id',
        'employee_expense_type_id',
        'employee_id',
        'deduction_reason_id',
        'salary_month_id',
        'year',
        'salary_amount',
        'deduction_amount',
        'will_get_total',
        'remarks'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function employee(){
        return $this->belongsTo(Employee::class, 'employee_id');
    }
    public function deduction_reason(){
        return $this->belongsTo(DeductionReason::class, 'deduction_reason_id');
    }
}
