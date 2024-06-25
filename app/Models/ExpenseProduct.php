<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseProduct extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function company(){
        return $this->belongsTo(Company::class);
    }
    public function branch(){
        return $this->belongsTo(Branch::class);
    }
    public function shop(){
        return $this->belongsTo(Shop::class);
    }
    public function expense_sector(){
        return $this->belongsTo(ExpenseSector::class);
    }
    public function expense_category(){
        return $this->belongsTo(ExpenseCategory::class);
    }
    public function purchaser(){
        return $this->belongsTo(ManagePurchaser::class);
    }
    //get total_unit_price
    public function expenseProductDetails(){
        return $this->hasMany(ExpenseProductDetail::class, 'expense_product_expense_id');
    }
    public static function getProductExpenseDataForPayment($expenseId)
    {
        return self::with([
                'company:id,name',
                'branch:id,name',
                'expense_category:id,name',
                'expense_sector:id,name',
                'shop:id,name,owner,location,phone_number_1',
                'purchaser:id,name,phone_number',
                'expenseProductDetails:id,expense_product_expense_id,total_unit_price' 

            ])
            ->where('id', $expenseId)
            ->first();
    }
}
