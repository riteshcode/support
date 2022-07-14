<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserBusiness extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'sub_subscription_db';
    protected $primaryKey = "subscription_db_id";
    protected $fillable = [
        'subscription_db_suffix',
        'users_id',
        'users_email',
        'subscription_id',
    ];

    public function subscription(){
        return $this->belongsTo(Subscription::class, 'subscription_id', 'subscription_id');
    }

    // public function business(){
    //     return $this->belongsTo(Business::class, 'business_id', 'business_id');
    // }

    // public function users(){
    //    return $this->belongsTo(Business::class, 'user_id', 'id');
    // }
}
