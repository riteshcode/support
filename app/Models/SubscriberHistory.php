<?php



namespace App\Models;



use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;



class SubscriberHistory extends Model

{

    use HasFactory;

    protected $fillable = [

        'subs_history_id',

        'subscription_id',

        'subs_plan',

        'subs_amount',

        'transaction_id',

        'date_added'

    ];

    public function getTable()
    {
        return config('dbtable.common_sub_subscription_history');
    }

    public function transaction(){

        return $this->belongsTo(Transaction::class, 'transaction_id', 'transaction_id');

    }

    public function plans(){
        return $this->belongsTo(SubscriptionPlan::class, 'subs_plan', 'plans_id');
    }
}

