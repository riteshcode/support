<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SubscriptionTransaction;

class SubscriptionHistory extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $primaryKey = 'subs_history_id';
    protected $fillable = [
        'subs_history_id',
        'subscription_id',
        'plan_id',
        'plan_name',
        'plan_amount',
        'plan_discount',
        'plan_duration',
        'approval_status',
        'approved_at',
        'expired_at',
        'created_at',
    ];

    public function getTable(){
        return config('dbtable.common_sub_subscription_history');
    }

    public function subscription_transaction(){
        return $this->hasMany(SubscriptionTransaction::class, 'subs_history_id', 'subs_history_id');
    }
}

