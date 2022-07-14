<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationSetting extends Model
{
    use HasFactory;
    
    protected $table = "notification_setting";

    protected $fillable = [
        'order_noti_status',
        'order_noti_type',
        'user_noti_status',
        'user_noti_type',
        'newsletter_noti_status',
        'newsletter_noti_type',
        'created_by'
    ];

}
