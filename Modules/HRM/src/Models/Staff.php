<?php

namespace Modules\HRM\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Modules\HRM\Models\Department;

class Staff extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'user_id',
        'department_id',
        'gender',
        'date_of_joining',
        'date_of_leaving',
        'marital_status',
        'date_of_birth',
        'state',
        'city',
        'zipcode',
        'contact_no',
        'address',
        'salary',
        'created_by',
        'update_by',
    ];

    public function getTable(){
        return config('dbtable.common_staffs');
    }

    public function user(){
        return $this->belongsTo(User::class,'user_id','id');
    }
    
    public function department(){
        return $this->belongsTo(Department::class,'department_id','department_id');
    }

}
