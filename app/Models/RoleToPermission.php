<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoleToPermission extends Model
{
    use HasFactory;
    protected $table = "sa_role_has_permissions";
    public $timestamps = false;
    protected $fillable = [
        'permission_id',
        'role_id',
    ];

    public function permission_info() {
        return $this->belongsTo(Permission::class,'permission_id','id');
    }

}
