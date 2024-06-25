<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RentalExpenseType extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function expenseRental(){
        return $this->hasMany(ExpenseRental::class);
    }
}
