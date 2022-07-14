<?php

namespace Modules\HRM\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'info',
        'no_of_days',
        'max_allowed',
    ];

    public function getTable(){
        return config('dbtable.common_leave_type');
    }

}
