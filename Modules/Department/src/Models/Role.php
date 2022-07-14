<?php

namespace Modules\Department\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'slug',
        'display_name',
        'created_by',
    ];

    public function permissions() {
       return $this->belongsToMany(Permission::class,'sa_role_has_permissions');
    }

    public function users() {
       return $this->belongsToMany(User::class,'users_roles');       
    }

    // public function permissions(){
    //     return $this->hasMany(RoleToPermission::class,'role_id', 'id');
    // }
    
    public function getTable()
    {
        return config('dbtable.common_roles');
    }

}
