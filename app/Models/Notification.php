<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    // protected $table = 'su_notification_to_user';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'notification_id',
        'user_id',
        'is_read',
        'read_at',
    ];
    public $timestamps = false;

    public function getNotiMessage(){
        return $this->belongsTo(NotificationMessage::class,'notification_id','notification_id');
    }

    public function userInfo(){
        return $this->belongsTo(User::class,'user_id','id');
    }
     public function getTable()
    {
        return config('dbtable.common_notification_to_user');
    }


}

