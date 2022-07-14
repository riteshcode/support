<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsersToBusiness extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = "su_users_to_business";

    protected $fillable = [
        'users_business_id',
        'users_id',
        'users_email',
        'database_id',
        'subscription_id',
        'business_id',
        'date_added'
    ];

    public function subscription(){
        return $this->belongsTo(Subscription::class, 'subscription_id', 'subscription_id');
    }
    
    public function business(){
        return $this->belongsTo(Business::class, 'business_id', 'business_id');
    }

    public function users(){
       return $this->belongsTo(Business::class, 'user_id', 'id'); 
    }
}
