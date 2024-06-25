<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Month extends Model
{
    use HasFactory;

    protected $table = 'months';

    protected $fillable = [
        'month_name'
    ];
    public function expenseRental(){
        return $this->hasMany(ExpenseRental::class);
    }
    public function expenseUtility(){
        return $this->hasMany(ExpenseUtility::class);
    }
}
