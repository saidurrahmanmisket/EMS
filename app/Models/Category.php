<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function expense_category(){
        return $this->hasMany(ExpenseCategory::class);
    }
    public function sub_category(){
        return $this->hasMany(SubCategory::class);
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
}
