<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseEmployee extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
    public function expenseCategory()
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }
    public function expenseSector()
    {
        return $this->belongsTo(ExpenseSector::class, 'expense_sector_id');
    }
    public function employee(){
        return $this->belongsTo(Employee::class, 'employee_id');
    }
    public function deduction_reason(){
        return $this->belongsTo(DeductionReason::class, 'deduction_reason_id');
    }
    public function employeeExpenseCategory(){
        return $this->belongsTo(EmployeeExpenseCategory::class, 'employee_expense_category_id');
    }
    public function employeeExpenseType(){
        return $this->belongsTo(EmployeeExpenseType::class, 'employee_expense_type_id');
    }
    public function salaryMonth(){
        return $this->belongsTo(SalaryMonth::class, 'salary_month_id');
    }

    public static function getEmployeeExpenseDataForPayment($expenseId)
    {
        return self::with([
                'company:id,name',
                'branch:id,name',
                'expenseCategory:id,name',
                'expenseSector:id,name',
                'employee:id,name,employee_code,phone_number',
            ])
            ->where('id', $expenseId)
            ->first();
    }

}
