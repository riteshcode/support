<?php

namespace Modules\HRM\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'dept_name',
        'dept_details',
        'status',
        'created_by',
    ];

    public function getTable(){
        return config('dbtable.common_departments');
    }

}
