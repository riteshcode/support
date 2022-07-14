<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLoginHistory extends Model
{
    use HasFactory;
    protected $table = "usr_user_logins";

    public $timestamps = true;

    protected $fillable = [
        "user_id",
        "user_ip",
        "location",
        "browser",
        "os",
        "longitude",
        "latitude",
        "city",
        "country_id"
    ];

}