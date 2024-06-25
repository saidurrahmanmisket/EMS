<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseSector extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function company(){
        return $this->belongsTo(Company::class, 'company_id');
    }
    public function category(){
        return $this->belongsTo(Category::class, 'category_id');
    }
    public function expense_category(){
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }
    public function employee_expense_category(){
        return $this->hasMany(EmployeeExpenseCategory::class);
    }
    public function expense_employee_regular(){
        return $this->hasMany(ExpenseEmployeeRegular::class);
    }
    //Expense Employee
    public function expenseEmployee()
    {
        return $this->hasMany(ExpenseEmployee::class);
    }
    public function expenseRental(){
        return $this->hasMany(ExpenseRental::class);
    }
    public function expenseUtility(){
        return $this->hasMany(ExpenseUtility::class);
    }
}
