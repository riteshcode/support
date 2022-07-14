<?php

namespace Modules\HRM\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'leave_type_id',
        'subject',
        'reason',
        'from_date',
        'to_date',
        'total_leave_days',
        'status',
    ];

    public function getTable(){
        return config('dbtable.common_leave');
    }

}
