<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SubscriberBusiness;
use App\Models\SubscriptionHistory;
use App\Models\SubscriptionTransaction;
use App\Models\Industry;

class Subscription extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $primaryKey = 'subscription_id';
    protected $fillable = [
        'subscription_id',
        'subscription_unique_id',
        'business_id',
        'industry_id',
        'status',
        'approved_at',
        'expired_at',
        'created_at',
        'updated_at',
    ];

    public function getTable(){
        return config('dbtable.common_sub_subscription');
    }

    public function subscriber_business(){
        return $this->belongsTo(SubscriberBusiness::class, 'business_id', 'business_id');
    }

    public function subscription_history(){
        return $this->hasMany(SubscriptionHistory::class, 'subscription_id', 'subscription_id');
    }

    public function subscription_transaction(){
        return $this->hasMany(SubscriptionTransaction::class, 'subscription_id', 'subscription_id');
    }

    public function industry_details(){ 
        return $this->belongsTo(Industry::class, 'industry_id', 'industry_id');
    }
}
