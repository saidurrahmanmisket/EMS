<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
//    protected $hidden = [
//        'password'
//    ];

    public function branch(){
        return $this->hasMany(Branch::class);
    }
    public function department(){
        return $this->hasMany(Department::class);
    }
    public function designation(){
        return $this->hasMany(Designation::class);
    }

    public function rentalSpace()
    {
      return $this->hasMany(RentalSpace::class);
    }

    public function managePurchaser()
    {
      return $this->hasMany(ManagePurchaser::class);
    }
    public function manageBank()
    {
      return $this->hasMany(ManageBank::class);
    }
    public function manageMobileBankingOperator()
    {
      return $this->hasMany(ManageMobileBankingOperator::class);
    }
    public function manageMerchantMobileBankingName()
    {
      return $this->hasMany(ManageMerchantMobileBankingName::class);
    }
    public function manageMerchantMobileBankingNumber()
    {
      return $this->hasMany(ManageMerchantMobileBankingNumber::class);
    }
    public function manageUtilitySector()
    {
      return $this->hasMany(ManageUtilitySector::class);
    }
    public function manageBillingType()
    {
      return $this->hasMany(ManageBillingType::class);
    }
    public function manageUtilityBillingSectorInfo()
    {
      return $this->hasMany(ManageUtilityBillingSectorInformation::class);
    }
    public function shop(){
        return $this->hasMany(Shop::class);
    }
    public function product(){
        return $this->hasMany(Product::class);
    }
    public function expense_category(){
        return $this->hasMany(ExpenseCategory::class);
    }
    public function expense_sector(){
        return $this->hasMany(ExpenseSector::class);
    }
    public function employee_expense_category(){
        return $this->hasMany(EmployeeExpenseCategory::class);
    }
    public function employee_expense_type(){
        return $this->hasMany(EmployeeExpenseType::class);
    }
    public function giver(){
        return $this->hasMany(Giver::class);
    }
    public function receiver(){
        return $this->hasMany(Receiver::class);
    }
    public function payment_client_giver(){
        return $this->hasMany(PaymentClientGiver::class);
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


    public function expense_product(){
        return $this->hasMany(ExpenseProduct::class);
    }
}
