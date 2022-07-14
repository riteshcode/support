<?php

namespace Modules\HRM\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Attendance extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'check_in',
        'check_out',
        'day',
        'month',
        'year',
        'attendance_date',
    ];

    public function getTable(){
        return config('dbtable.common_attendance');
    }

    public function user(){
        return $this->belongsTo(User::class,'user_id', 'id');
    }

}
