<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryMonth extends Model
{
    use HasFactory;

    protected $table = 'salary_months';

    protected $fillable = [
        'month_name'
    ];
     //Expense Employee
     public function expenseEmployee()
     {
         return $this->hasMany(ExpenseEmployee::class);
     }
}
