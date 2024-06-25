<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionType extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function company(){
        return $this->belongsTo(Company::class, 'company_id');
    }
    public function branch(){
        return $this->belongsTo(Branch::class, 'branch_id');
    }
    public function category(){
        return $this->belongsTo(Category::class, 'category_id');
    }
    public function receive_method(){
        return $this->belongsTo(ReceiveMethod::class, 'receive_method_id');
    }
}
