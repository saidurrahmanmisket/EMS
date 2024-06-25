<?php

namespace App\Models;

use App\Http\Controllers\TransactionTypeController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceiveMethod extends Model
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
    public function transaction_type(){
        return $this->hasMany(TransactionType::class);
    }
}
