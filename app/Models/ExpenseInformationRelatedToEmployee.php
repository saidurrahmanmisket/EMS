<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseInformationRelatedToEmployee extends Model
{
    use HasFactory;

    protected $table = 'expense_information_related_to_employees';

    protected $fillable = [
        'total_advance_amount_given',
        'employee_id'
    ];
    public function employee(){
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
