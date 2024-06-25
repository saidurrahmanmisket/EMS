<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;


class FrontendRolePermission extends Model
{
    use HasFactory;

    protected $table = 'frontend_role_permissions';
    protected $fillable = [
        'role_id',
        'module_id',
        'insert_',
        'view_',
        'update_',
        'delete_',
        'manage_'
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
    public function module()
    {
        return $this->belongsTo(Module::class);
    }
}
