<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankAccounts extends Model
{
    use HasFactory;
    public function bank()
    {
        return $this->belongsTo(ManageBank::class, 'bank_id');
    }

}
