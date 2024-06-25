<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;
    protected $guarded = [];


    public function company(){
        return $this->belongsTo(Company::class, 'company_id');
    }
    public function rentalSpace()
    {
      return $this->hasMany(RentalSpace::class);
    }
    public function managePurchaser()
    {
      return $this->hasMany(ManagePurchaser::class);
    }
    public function manageUtilityBillingSectorInfo()
    {
      return $this->hasMany(ManageUtilityBillingSectorInformation::class);
    }
    public function payment_receiver(){
        return $this->hasMany(PaymentReceiver::class);
    }
    public function receive_method(){
        return $this->hasMany(ReceiveMethod::class);
    }
    public function transaction_type(){
        return $this->hasMany(TransactionType::class);
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
