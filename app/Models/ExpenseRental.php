<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseRental extends Model
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
    public function rentalSpace()
    {
        return $this->belongsTo(RentalSpace::class, 'rental_space_id');
    }
    public function rentalExpenseType()
    {
        return $this->belongsTo(RentalExpenseType::class, 'rental_expense_type_id');
    }
    public function rentalGivenMonth()
    {
        return $this->belongsTo(Month::class, 'month_id');
    }

    public static function getRentalExpenseDataForPayment($expenseId)
    {
        return self::with([
                    'company:id,name',
                    'branch:id,name',
                    'expenseCategory:id,name',
                    'expenseSector:id,name',
                    'rentalExpenseType:id,rental_type_name',
                    'rentalSpace:id,rental_space_name,rental_code',
                    'rentalSpace.rentalSpaceOwner',
                    'rentalSpace.rentalSpaceCity'
                ])
                    ->where('id', $expenseId)
                    ->first();
    }
}
