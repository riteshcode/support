<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
// use Spatie\Permission\Traits\HasRoles;
use Modules\Department\Traits\HasPermissionsTrait;
use Modules\HRM\Models\Designation;
use Modules\HRM\Models\Staff;
class User extends Authenticatable
{
    use HasFactory, Notifiable;
    use HasPermissionsTrait;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'contact',
        'title',
        'profile_photo',
        'designation_id',
        'bio',
        'status',
        'api_token',
        'created_by'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        // 'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getTable()
    {
        return config('dbtable.common_users');
    }
    
    public function all_notification(){
        return $this->hasMany(Notification::class,'user_id','id');
    }

    public function unread_notification($user_id){
        $list = Notification::where('user_id',$user_id)->where('is_read',0)->get();
        return $list;
    }

    public function designation(){
        return $this->belongsTo(Designation::class, 'designation_id', 'designation_id');
    }

    public function staff(){
        return $this->hasMany(Staff::class, 'user_id', 'id');
    }

}
