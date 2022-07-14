<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;
    // protected $table = "sa_permissions";

    protected $fillable = [
        'name',
        'guard_name',
    ];
    
    public function getTable()
    {
        return config('dbtable.common_permissions');
    }

}
