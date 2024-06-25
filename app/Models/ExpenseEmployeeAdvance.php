<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseEmployeeAdvance extends Model
{
    use HasFactory;

    protected $table = 'expense_employee_advances';

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
        'advance_amount_given',
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
}
