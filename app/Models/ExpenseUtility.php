<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseUtility extends Model
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
    public function manageUtilityBillingSector()
    {
        return $this->belongsTo(ManageUtilitySector::class, 'manage_utility_sector_id');
    }
    public function manageBillingType()
    {
        return $this->belongsTo(ManageBillingType::class, 'manage_billing_type_id');
    }
    public function Month()
    {
        return $this->belongsTo(Month::class, 'month_id');
    }
    public static function getutilityExpenseDataForPayment($expenseId)
    {
        return self::with([
            'company:id,name',
            'branch:id,name',
            'expenseCategory:id,name',
            'expenseSector:id,name',
            'manageUtilityBillingSector:id,utility_billing_sector_name,billing_sector_code',
            'manageUtilityBillingSector.manageUtilityBillingSectorInfo',
            'rentalSpace:id,rental_space_name,rental_code',
            'rentalSpace.rentalSpaceOwner',
            'rentalSpace.rentalSpaceCity',
            'month'
        ])
            ->where('id', $expenseId)
            ->first();
    }


}
